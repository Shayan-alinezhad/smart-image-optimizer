<?php
/**
 * Settings page template.
 *
 * @package SmartImageOptimizer
 *
 * @var \SmartImageOptimizer\Settings $settings
 * @var array $values
 * @var bool  $webp_supported
 * @var bool  $imagick
 * @var bool  $gd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bulk_url = admin_url( 'upload.php?page=sio-bulk' );
$logs_url = admin_url( 'admin.php?page=sio-logs' );
?>
<div class="<?php echo esc_attr( sio_wrap_classes() ); ?>">
	<?php
	sio_render_partial(
		'header',
		array(
			'sio_page_title' => __( 'Smart Image Optimizer & Auto WebP', 'smart-image-optimizer' ),
			'sio_page_icon'  => 'dashicons-images-alt2',
		)
	);
	?>

	<?php settings_errors( \SmartImageOptimizer\Settings::OPTION_KEY ); ?>

	<?php if ( ! $webp_supported ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'WebP encoding is not available on this server. Images will still be resized and compressed, but not converted to WebP.', 'smart-image-optimizer' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php" class="sio-form">
		<?php settings_fields( \SmartImageOptimizer\Settings::OPTION_GROUP ); ?>
		<?php $opt = \SmartImageOptimizer\Settings::OPTION_KEY; ?>

		<div class="sio-grid">
			<div class="sio-main">

				<!-- General -->
				<div class="sio-card">
					<h2><?php esc_html_e( 'General', 'smart-image-optimizer' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="sio-admin-language"><?php esc_html_e( 'Interface Language', 'smart-image-optimizer' ); ?></label></th>
							<td>
								<select id="sio-admin-language" name="<?php echo esc_attr( $opt ); ?>[admin_language]">
									<?php foreach ( \SmartImageOptimizer\I18n::choices() as $sio_code => $sio_label ) : ?>
										<option value="<?php echo esc_attr( $sio_code ); ?>" <?php selected( isset( $values['admin_language'] ) ? $values['admin_language'] : 'fa', $sio_code ); ?>>
											<?php echo esc_html( $sio_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Change the language of this plugin\'s admin pages. This does not affect the rest of your site.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable Plugin', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[enable_plugin]" value="1" <?php checked( $values['enable_plugin'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Automatically optimize new uploads.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable WebP', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[enable_webp]" value="1" <?php checked( $values['enable_webp'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Generate a WebP version of each uploaded image.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable Resize', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[enable_resize]" value="1" <?php checked( $values['enable_resize'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Downscale images that exceed the maximum dimensions.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Keep Originals', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[keep_originals]" value="1" <?php checked( $values['keep_originals'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Back up the original file before optimizing. Disable to delete originals after a successful WebP conversion.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Overwrite Existing', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[overwrite_existing]" value="1" <?php checked( $values['overwrite_existing'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Re-optimize images that were already optimized.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- WebP & Compression -->
				<div class="sio-card">
					<h2><?php esc_html_e( 'WebP & Compression', 'smart-image-optimizer' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="sio-quality"><?php esc_html_e( 'Quality', 'smart-image-optimizer' ); ?></label></th>
							<td>
								<input type="range" id="sio-quality" min="1" max="100" step="1" name="<?php echo esc_attr( $opt ); ?>[quality]" value="<?php echo esc_attr( $values['quality'] ); ?>" data-sio-range="sio-quality-value" />
								<output id="sio-quality-value"><?php echo esc_html( $values['quality'] ); ?></output>
								<p class="description"><?php esc_html_e( 'WebP / JPEG quality. 85 is a good balance of size and clarity.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Lossless WebP', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[lossless]" value="1" <?php checked( $values['lossless'], 1 ); ?> <?php disabled( ! $imagick ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description">
									<?php esc_html_e( 'Use lossless WebP encoding (larger files, requires Imagick).', 'smart-image-optimizer' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Strip Metadata', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[strip_metadata]" value="1" <?php checked( $values['strip_metadata'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Remove unnecessary EXIF / metadata to reduce file size.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Preserve Color Profile', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[preserve_icc]" value="1" <?php checked( $values['preserve_icc'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Keep the ICC color profile when stripping metadata (Imagick).', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Fix Orientation', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[fix_orientation]" value="1" <?php checked( $values['fix_orientation'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Auto-rotate images based on EXIF orientation.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- Resize -->
				<div class="sio-card">
					<h2><?php esc_html_e( 'Resize', 'smart-image-optimizer' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="sio-max-width"><?php esc_html_e( 'Maximum Width (px)', 'smart-image-optimizer' ); ?></label></th>
							<td><input type="number" id="sio-max-width" min="0" step="1" class="small-text" name="<?php echo esc_attr( $opt ); ?>[max_width]" value="<?php echo esc_attr( $values['max_width'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="sio-max-height"><?php esc_html_e( 'Maximum Height (px)', 'smart-image-optimizer' ); ?></label></th>
							<td><input type="number" id="sio-max-height" min="0" step="1" class="small-text" name="<?php echo esc_attr( $opt ); ?>[max_height]" value="<?php echo esc_attr( $values['max_height'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Maintain Aspect Ratio', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[maintain_aspect]" value="1" <?php checked( $values['maintain_aspect'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Prevent Upscaling', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[prevent_upscaling]" value="1" <?php checked( $values['prevent_upscaling'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description"><?php esc_html_e( 'Only shrink images; never enlarge smaller ones.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- Advanced -->
				<div class="sio-card">
					<h2><?php esc_html_e( 'Advanced', 'smart-image-optimizer' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="sio-batch"><?php esc_html_e( 'Bulk Batch Size', 'smart-image-optimizer' ); ?></label></th>
							<td>
								<input type="number" id="sio-batch" min="1" max="50" step="1" class="small-text" name="<?php echo esc_attr( $opt ); ?>[batch_size]" value="<?php echo esc_attr( $values['batch_size'] ); ?>" />
								<p class="description"><?php esc_html_e( 'How many images to process per request during bulk optimization.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sio-skip-large"><?php esc_html_e( 'Skip Large Files (MB)', 'smart-image-optimizer' ); ?></label></th>
							<td>
								<input type="number" id="sio-skip-large" min="0" max="512" step="1" class="small-text" name="<?php echo esc_attr( $opt ); ?>[skip_large_mb]" value="<?php echo esc_attr( isset( $values['skip_large_mb'] ) ? $values['skip_large_mb'] : 0 ); ?>" />
								<p class="description"><?php esc_html_e( 'Skip images larger than this size. Set to 0 to disable the limit.', 'smart-image-optimizer' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable Logging', 'smart-image-optimizer' ); ?></th>
							<td>
								<label class="sio-switch">
									<input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[enable_logging]" value="1" <?php checked( $values['enable_logging'], 1 ); ?> />
									<span class="sio-slider"></span>
								</label>
								<p class="description">
									<?php
									printf(
										/* translators: %s: logs page URL */
										wp_kses_post( __( 'Record optimization activity. <a href="%s">View logs</a>.', 'smart-image-optimizer' ) ),
										esc_url( $logs_url )
									);
									?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<?php submit_button( __( 'Save Settings', 'smart-image-optimizer' ) ); ?>
			</div>

			<div class="sio-side">
				<div class="sio-card">
					<h2><?php esc_html_e( 'Bulk Optimization', 'smart-image-optimizer' ); ?></h2>
					<p><?php esc_html_e( 'Optimize images that already exist in your media library.', 'smart-image-optimizer' ); ?></p>
					<a class="button button-secondary" href="<?php echo esc_url( $bulk_url ); ?>"><?php esc_html_e( 'Open Bulk Optimizer', 'smart-image-optimizer' ); ?></a>
				</div>

				<div class="sio-card">
					<h2><?php esc_html_e( 'Server Capabilities', 'smart-image-optimizer' ); ?></h2>
					<ul class="sio-caps">
						<li><span><?php esc_html_e( 'Imagick', 'smart-image-optimizer' ); ?></span> <?php echo $imagick ? '<strong class="sio-ok">' . esc_html__( 'Available', 'smart-image-optimizer' ) . '</strong>' : '<strong class="sio-no">' . esc_html__( 'Missing', 'smart-image-optimizer' ) . '</strong>'; ?></li>
						<li><span><?php esc_html_e( 'GD', 'smart-image-optimizer' ); ?></span> <?php echo $gd ? '<strong class="sio-ok">' . esc_html__( 'Available', 'smart-image-optimizer' ) . '</strong>' : '<strong class="sio-no">' . esc_html__( 'Missing', 'smart-image-optimizer' ) . '</strong>'; ?></li>
						<li><span><?php esc_html_e( 'WebP Encoding', 'smart-image-optimizer' ); ?></span> <?php echo $webp_supported ? '<strong class="sio-ok">' . esc_html__( 'Supported', 'smart-image-optimizer' ) . '</strong>' : '<strong class="sio-no">' . esc_html__( 'Unsupported', 'smart-image-optimizer' ) . '</strong>'; ?></li>
					</ul>
				</div>

				<div class="sio-card sio-card--brand">
					<h2><?php esc_html_e( 'Support & Documentation', 'smart-image-optimizer' ); ?></h2>
					<p><?php esc_html_e( 'Developed by Cloner', 'smart-image-optimizer' ); ?></p>
					<a class="button button-secondary" href="<?php echo esc_url( SIO_AUTHOR_URL ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Visit clonerr.ir', 'smart-image-optimizer' ); ?>
					</a>
				</div>
			</div>
		</div>
	</form>

	<?php sio_render_partial( 'footer' ); ?>
</div>
