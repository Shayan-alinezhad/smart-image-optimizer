<?php
/**
 * Low-level image processing built on wp_get_image_editor.
 *
 * @package SmartImageOptimizer
 */

namespace SmartImageOptimizer\Image;

use SmartImageOptimizer\Settings;
use SmartImageOptimizer\Logger;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles reading, orientation, resizing, compression and WebP encoding.
 */
final class Processor {

	/**
	 * Settings service.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Logger service.
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings service.
	 * @param Logger   $logger   Logger service.
	 */
	public function __construct( Settings $settings, Logger $logger ) {
		$this->settings = $settings;
		$this->logger   = $logger;
	}

	/**
	 * Supported source mime types.
	 *
	 * @return array
	 */
	public function supported_mimes() {
		return (array) $this->settings->get( 'allowed_types' );
	}

	/**
	 * Whether a mime type is supported.
	 *
	 * @param string $mime Mime type.
	 * @return bool
	 */
	public function is_supported( $mime ) {
		return in_array( $mime, $this->supported_mimes(), true );
	}

	/**
	 * Whether the server can encode WebP.
	 *
	 * @return bool
	 */
	public function webp_supported() {
		return sio_webp_supported();
	}

	/**
	 * Read basic image info: width, height, mime.
	 *
	 * @param string $path Absolute path.
	 * @return array|WP_Error
	 */
	public function read_image_info( $path ) {
		$size = @getimagesize( $path );
		if ( false === $size ) {
			$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
			if ( 'bmp' === $ext ) {
				return array(
					'width'  => 0,
					'height' => 0,
					'mime'   => 'image/bmp',
				);
			}
			return new WP_Error( 'sio_bad_image', __( 'Could not read image dimensions.', 'smart-image-optimizer' ) );
		}

		$mime = isset( $size['mime'] ) ? $size['mime'] : '';
		if ( 'image/x-ms-bmp' === $mime ) {
			$mime = 'image/bmp';
		}

		return array(
			'width'  => (int) $size[0],
			'height' => (int) $size[1],
			'mime'   => $mime,
		);
	}

	/**
	 * Prepare an image editor with orientation, resize, quality and metadata
	 * stripping applied. The caller is responsible for saving the result.
	 *
	 * @param string $source_path Absolute source path.
	 * @return array|WP_Error {
	 *     @type int              $original_size Bytes.
	 *     @type int              $width         Source width.
	 *     @type int              $height        Source height.
	 *     @type string           $mime          Source mime type.
	 *     @type bool             $resized       Whether a resize happened.
	 *     @type \WP_Image_Editor $editor        Prepared editor.
	 *     @type string|null      $temp_path     Temp file to clean up, if any.
	 * }
	 */
	public function process( $source_path ) {
		if ( ! file_exists( $source_path ) || ! is_readable( $source_path ) ) {
			return new WP_Error( 'sio_missing_file', __( 'Source file not found or unreadable.', 'smart-image-optimizer' ) );
		}

		$original_size = (int) filesize( $source_path );
		$info          = $this->read_image_info( $source_path );
		if ( is_wp_error( $info ) ) {
			return $info;
		}

		// BMP is not natively handled by every editor; normalise to PNG first.
		$working_path = $source_path;
		$temp_created = null;
		if ( 'image/bmp' === $info['mime'] ) {
			$converted = $this->convert_bmp_to_png( $source_path );
			if ( is_wp_error( $converted ) ) {
				return $converted;
			}
			$working_path = $converted;
			$temp_created = $converted;
		}

		// Prefer Imagick, fall back to GD (WordPress decides via this filter set).
		$editor = wp_get_image_editor(
			$working_path,
			array( 'methods' => array( 'resize', 'save' ) )
		);
		if ( is_wp_error( $editor ) ) {
			$this->cleanup_temp( $temp_created );
			return $editor;
		}

		// Fix EXIF orientation for JPEGs.
		if ( $this->settings->is_enabled( 'fix_orientation' ) ) {
			$this->fix_orientation( $editor, $working_path, $info['mime'] );
		}

		// GIF: keep only the first frame.
		if ( 'image/gif' === $info['mime'] ) {
			$this->flatten_first_frame( $editor );
		}

		// Resize if enabled and needed.
		$resized = false;
		if ( $this->settings->is_enabled( 'enable_resize' ) ) {
			$resized = $this->maybe_resize( $editor );
		}

		// Compression / quality.
		$quality = (int) $this->settings->get( 'quality', 85 );
		$editor->set_quality( $quality );

		// Strip metadata / EXIF, optionally preserving the ICC profile.
		if ( $this->settings->is_enabled( 'strip_metadata' ) ) {
			$this->strip_metadata( $editor );
		}

		return array(
			'original_size' => $original_size,
			'width'         => $info['width'],
			'height'        => $info['height'],
			'mime'          => $info['mime'],
			'resized'       => $resized,
			'editor'        => $editor,
			'temp_path'     => $temp_created,
		);
	}

	/**
	 * Save the prepared editor as a WebP file.
	 *
	 * @param \WP_Image_Editor $editor      Prepared editor.
	 * @param string           $destination Absolute destination path.
	 * @return array|WP_Error
	 */
	public function save_webp( $editor, $destination ) {
		if ( $this->settings->is_enabled( 'lossless' ) ) {
			$this->set_lossless( $editor );
		}

		$saved = $editor->save( $destination, 'image/webp' );
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}
		if ( empty( $saved['path'] ) || ! file_exists( $saved['path'] ) ) {
			return new WP_Error( 'sio_webp_failed', __( 'WebP file was not created.', 'smart-image-optimizer' ) );
		}
		return $saved;
	}

	/**
	 * Save the prepared editor in its original mime type (in-place optimize).
	 *
	 * @param \WP_Image_Editor $editor      Prepared editor.
	 * @param string           $destination Absolute destination path.
	 * @param string           $mime        Mime type.
	 * @return array|WP_Error
	 */
	public function save_as( $editor, $destination, $mime ) {
		// BMP normalises to PNG for in-place saves.
		if ( 'image/bmp' === $mime ) {
			$mime        = 'image/png';
			$destination = preg_replace( '/\\.bmp$/i', '.png', $destination );
		}
		$saved = $editor->save( $destination, $mime );
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}
		if ( empty( $saved['path'] ) || ! file_exists( $saved['path'] ) ) {
			return new WP_Error( 'sio_save_failed', __( 'Optimized file was not created.', 'smart-image-optimizer' ) );
		}
		return $saved;
	}

	/**
	 * Delete a temp file if one was created.
	 *
	 * @param string|null $temp_path Temp path.
	 */
	public function cleanup_temp( $temp_path ) {
		if ( $temp_path && file_exists( $temp_path ) ) {
			@unlink( $temp_path );
		}
	}

	/**
	 * Resize only when the image exceeds the configured maximums.
	 *
	 * @param \WP_Image_Editor $editor Editor.
	 * @return bool True when a resize was performed.
	 */
	private function maybe_resize( $editor ) {
		$size = $editor->get_size();
		$w    = isset( $size['width'] ) ? (int) $size['width'] : 0;
		$h    = isset( $size['height'] ) ? (int) $size['height'] : 0;

		if ( $w <= 0 || $h <= 0 ) {
			return false;
		}

		$max_w = (int) $this->settings->get( 'max_width', 1920 );
		$max_h = (int) $this->settings->get( 'max_height', 1920 );

		$exceeds = ( $max_w > 0 && $w > $max_w ) || ( $max_h > 0 && $h > $max_h );

		// Prevent upscaling: only resize when the image is larger than the cap.
		if ( ! $exceeds ) {
			return false;
		}

		$maintain_aspect = $this->settings->is_enabled( 'maintain_aspect' );
		$target_w        = $max_w > 0 ? $max_w : null;
		$target_h        = $max_h > 0 ? $max_h : null;

		// crop = false keeps the aspect ratio; crop = true forces exact box.
		$crop   = ! $maintain_aspect;
		$result = $editor->resize( $target_w, $target_h, $crop );

		if ( is_wp_error( $result ) ) {
			$this->logger->warning( 'Resize failed: ' . $result->get_error_message() );
			return false;
		}
		return true;
	}

	/**
	 * Correct orientation using EXIF data (JPEG only).
	 *
	 * @param \WP_Image_Editor $editor Editor.
	 * @param string           $path   File path.
	 * @param string           $mime   Mime type.
	 */
	private function fix_orientation( $editor, $path, $mime ) {
		if ( 'image/jpeg' !== $mime || ! function_exists( 'exif_read_data' ) ) {
			return;
		}

		$exif = @exif_read_data( $path );
		if ( empty( $exif['Orientation'] ) ) {
			return;
		}

		switch ( (int) $exif['Orientation'] ) {
			case 2:
				$editor->flip( false, true );
				break;
			case 3:
				$editor->rotate( 180 );
				break;
			case 4:
				$editor->flip( true, false );
				break;
			case 5:
				$editor->rotate( -90 );
				$editor->flip( false, true );
				break;
			case 6:
				$editor->rotate( -90 );
				break;
			case 7:
				$editor->rotate( 90 );
				$editor->flip( false, true );
				break;
			case 8:
				$editor->rotate( 90 );
				break;
		}
	}

	/**
	 * Strip metadata/EXIF while optionally keeping the ICC colour profile.
	 *
	 * @param \WP_Image_Editor $editor Editor.
	 */
	private function strip_metadata( $editor ) {
		$imagick = $this->extract_imagick( $editor );
		if ( ! ( $imagick instanceof \Imagick ) ) {
			// GD does not carry EXIF into the re-encoded output anyway.
			return;
		}
		try {
			$icc = array();
			if ( $this->settings->is_enabled( 'preserve_icc' ) ) {
				$icc = $imagick->getImageProfiles( 'icc', true );
			}
			$imagick->stripImage();
			if ( ! empty( $icc['icc'] ) ) {
				$imagick->profileImage( 'icc', $icc['icc'] );
			}
		} catch ( \Exception $e ) {
			$this->logger->warning( 'Metadata strip skipped: ' . $e->getMessage() );
		}
	}

	/**
	 * Enable lossless WebP on the underlying Imagick object.
	 *
	 * @param \WP_Image_Editor $editor Editor.
	 */
	private function set_lossless( $editor ) {
		$imagick = $this->extract_imagick( $editor );
		if ( ! ( $imagick instanceof \Imagick ) ) {
			return;
		}
		try {
			$imagick->setImageFormat( 'webp' );
			$imagick->setOption( 'webp:lossless', 'true' );
		} catch ( \Exception $e ) {
			$this->logger->warning( 'Lossless WebP not applied: ' . $e->getMessage() );
		}
	}

	/**
	 * Reduce a multi-frame image (GIF) to its first frame.
	 *
	 * @param \WP_Image_Editor $editor Editor.
	 */
	private function flatten_first_frame( $editor ) {
		$data = $this->extract_imagick( $editor, true );
		if ( ! $data ) {
			return;
		}
		list( $imagick, $property, $instance ) = $data;
		if ( ! ( $imagick instanceof \Imagick ) ) {
			return;
		}
		try {
			if ( $imagick->getNumberImages() > 1 ) {
				$coalesced = $imagick->coalesceImages();
				$coalesced->setIteratorIndex( 0 );
				$first = new \Imagick();
				$first->addImage( $coalesced->getImage() );
				$first->setImageFormat( 'png' );
				$property->setValue( $instance, $first );
			}
		} catch ( \Exception $e ) {
			$this->logger->warning( 'GIF flatten skipped: ' . $e->getMessage() );
		}
	}

	/**
	 * Access the underlying Imagick object from a WP_Image_Editor_Imagick.
	 *
	 * @param \WP_Image_Editor $editor    Editor.
	 * @param bool             $with_meta Return [imagick, ReflectionProperty, editor].
	 * @return \Imagick|array|null
	 */
	private function extract_imagick( $editor, $with_meta = false ) {
		if ( ! is_a( $editor, 'WP_Image_Editor_Imagick' ) ) {
			return null;
		}
		try {
			$reflection = new \ReflectionClass( $editor );
			if ( ! $reflection->hasProperty( 'image' ) ) {
				return null;
			}
			$property = $reflection->getProperty( 'image' );
			$property->setAccessible( true );
			$imagick = $property->getValue( $editor );
			if ( $with_meta ) {
				return array( $imagick, $property, $editor );
			}
			return $imagick;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Convert a BMP file to a temporary PNG using GD.
	 *
	 * @param string $source Source path.
	 * @return string|WP_Error Temp path or error.
	 */
	private function convert_bmp_to_png( $source ) {
		if ( ! function_exists( 'imagecreatefrombmp' ) || ! function_exists( 'imagepng' ) ) {
			return new WP_Error( 'sio_bmp_unsupported', __( 'BMP support requires the GD extension.', 'smart-image-optimizer' ) );
		}
		$image = @imagecreatefrombmp( $source );
		if ( ! $image ) {
			return new WP_Error( 'sio_bmp_failed', __( 'Could not read the BMP image.', 'smart-image-optimizer' ) );
		}
		$temp = trailingslashit( sys_get_temp_dir() ) . 'sio-' . wp_generate_password( 12, false ) . '.png';
		imagepng( $image, $temp );
		imagedestroy( $image );
		if ( ! file_exists( $temp ) ) {
			return new WP_Error( 'sio_bmp_temp', __( 'Could not create a temporary BMP conversion.', 'smart-image-optimizer' ) );
		}
		return $temp;
	}
}
