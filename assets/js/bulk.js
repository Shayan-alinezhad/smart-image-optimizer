/**
 * Smart Image Optimizer - bulk optimization engine.
 * Vanilla JS. Drives sequential per-image AJAX requests with
 * progress, ETA, pause / resume / cancel. Lazy-loaded in the footer.
 */
( function () {
	'use strict';

	if ( typeof window.SIO_Bulk === 'undefined' ) {
		return;
	}

	var cfg = window.SIO_Bulk;
	var i18n = cfg.i18n || {};

	var state = {
		ids: [],
		index: 0,
		total: 0,
		running: false,
		paused: false,
		cancelled: false,
		force: false,
		optimized: 0,
		skipped: 0,
		errors: 0,
		savedBytes: 0,
		startTime: 0
	};

	var el = {};

	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	function sprintf( template ) {
		var args = Array.prototype.slice.call( arguments, 1 );
		var i = 0;
		return String( template ).replace( /%(\d+)\$[sd]|%[sd]/g, function ( match, pos ) {
			if ( pos ) {
				return args[ parseInt( pos, 10 ) - 1 ];
			}
			return args[ i++ ];
		} );
	}

	function request( action, data ) {
		var body = new URLSearchParams();
		body.append( 'action', action );
		body.append( 'nonce', cfg.nonce );
		Object.keys( data || {} ).forEach( function ( key ) {
			body.append( key, data[ key ] );
		} );

		return fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString()
		} ).then( function ( res ) {
			return res.json();
		} );
	}

	function humanTime( seconds ) {
		seconds = Math.max( 0, Math.round( seconds ) );
		var m = Math.floor( seconds / 60 );
		var s = seconds % 60;
		if ( m > 0 ) {
			return m + 'm ' + s + 's';
		}
		return s + 's';
	}

	function humanBytes( bytes ) {
		if ( ! bytes ) {
			return '0 B';
		}
		var units = [ 'B', 'KB', 'MB', 'GB' ];
		var i = Math.floor( Math.log( bytes ) / Math.log( 1024 ) );
		i = Math.min( i, units.length - 1 );
		return ( bytes / Math.pow( 1024, i ) ).toFixed( 2 ) + ' ' + units[ i ];
	}

	function setStatus( text ) {
		if ( el.status ) {
			el.status.textContent = text;
		}
	}

	function logLine( text, type ) {
		if ( ! el.log ) {
			return;
		}
		el.log.hidden = false;
		var line = document.createElement( 'div' );
		line.className = 'sio-log__line sio-log__line--' + ( type || 'info' );
		line.textContent = text;
		el.log.appendChild( line );
		el.log.scrollTop = el.log.scrollHeight;
	}

	function updateProgress() {
		var done = state.index;
		var pct = state.total > 0 ? Math.round( ( done / state.total ) * 100 ) : 0;
		el.bar.style.width = pct + '%';
		el.count.textContent = done + ' / ' + state.total;

		if ( done > 0 && state.running ) {
			var elapsed = ( Date.now() - state.startTime ) / 1000;
			var perItem = elapsed / done;
			var remaining = perItem * ( state.total - done );
			el.eta.textContent = sprintf( i18n.eta || 'ETA: %s', humanTime( remaining ) );
		} else if ( state.running ) {
			el.eta.textContent = sprintf( i18n.eta || 'ETA: %s', i18n.calculating || '...' );
		}
	}

	function toggleButtons() {
		el.start.disabled = state.running;
		el.pause.disabled = ! state.running || state.paused;
		el.resume.disabled = ! state.running || ! state.paused;
		el.cancel.disabled = ! state.running;
	}

	function refreshStats() {
		request( 'sio_get_stats', {} ).then( function ( res ) {
			if ( ! res || ! res.success ) {
				return;
			}
			var t = res.data;
			setStat( 'total_images', t.total_images );
			setStat( 'optimized_images', t.optimized_images );
			setStatText( 'saved_total_human', humanBytes( t.saved_total ) );
			setStatText( 'average_saved', ( parseFloat( t.average_saved ) || 0 ).toFixed( 1 ) + '%' );
		} );
	}

	function setStat( key, value ) {
		var node = document.querySelector( '[data-stat="' + key + '"]' );
		if ( node ) {
			node.textContent = value;
		}
	}

	function setStatText( key, value ) {
		setStat( key, value );
	}

	function processNext() {
		if ( state.cancelled ) {
			finish( true );
			return;
		}
		if ( state.paused ) {
			return;
		}
		if ( state.index >= state.total ) {
			finish( false );
			return;
		}

		var id = state.ids[ state.index ];
		setStatus( sprintf( i18n.processing || 'Processing %1$d of %2$d', state.index + 1, state.total ) );

		request( 'sio_bulk_optimize', { attachment_id: id, force: state.force ? 1 : 0 } )
			.then( function ( res ) {
				if ( res && res.success ) {
					var d = res.data;
					if ( 'optimized' === d.status ) {
						state.optimized++;
						state.savedBytes += parseInt( d.saved_bytes, 10 ) || 0;
						logLine( '✓ ' + d.title + ' — ' + ( d.human ? d.human.saved : '' ) + ' (' + ( d.percent || 0 ) + '%)', 'optimized' );
					} else if ( 'skipped' === d.status ) {
						state.skipped++;
						logLine( '– ' + ( i18n.skipped || 'Skipped' ) + ': ' + d.title + ' (' + d.message + ')', 'skipped' );
					} else {
						state.errors++;
						logLine( '✗ ' + ( i18n.error || 'Error' ) + ': ' + d.title + ' (' + d.message + ')', 'error' );
					}
				} else {
					state.errors++;
					var msg = res && res.data && res.data.message ? res.data.message : '';
					logLine( '✗ ' + ( i18n.error || 'Error' ) + ': #' + id + ' ' + msg, 'error' );
				}
			} )
			.catch( function () {
				state.errors++;
				logLine( '✗ ' + ( i18n.error || 'Error' ) + ': #' + id, 'error' );
			} )
			.then( function () {
				state.index++;
				updateProgress();
				processNext();
			} );
	}

	function finish( cancelled ) {
		state.running = false;
		state.paused = false;
		toggleButtons();
		updateProgress();
		refreshStats();

		if ( cancelled ) {
			setStatus( i18n.cancelled || 'Cancelled.' );
		} else {
			setStatus( sprintf( i18n.done || 'Done. Optimized %1$d, saved %2$s.', state.optimized, humanBytes( state.savedBytes ) ) );
		}
	}

	function start() {
		var scopeInput = document.querySelector( 'input[name="sio-scope"]:checked' );
		var scope = scopeInput ? scopeInput.value : 'unoptimized';
		state.force = ( 'all' === scope );

		state.running = true;
		state.paused = false;
		state.cancelled = false;
		state.index = 0;
		state.optimized = 0;
		state.skipped = 0;
		state.errors = 0;
		state.savedBytes = 0;
		state.startTime = Date.now();

		el.progressWrap.hidden = false;
		el.log.innerHTML = '';
		toggleButtons();
		setStatus( i18n.scanning || 'Scanning...' );

		request( 'sio_bulk_scan', { scope: scope } ).then( function ( res ) {
			if ( ! res || ! res.success ) {
				setStatus( i18n.error || 'Error' );
				state.running = false;
				toggleButtons();
				return;
			}
			state.ids = res.data.ids || [];
			state.total = state.ids.length;

			if ( state.total === 0 ) {
				setStatus( i18n.noImages || 'No images found.' );
				state.running = false;
				toggleButtons();
				return;
			}

			setStatus( i18n.starting || 'Starting...' );
			updateProgress();
			processNext();
		} );
	}

	ready( function () {
		el = {
			start: document.getElementById( 'sio-start' ),
			pause: document.getElementById( 'sio-pause' ),
			resume: document.getElementById( 'sio-resume' ),
			cancel: document.getElementById( 'sio-cancel' ),
			progressWrap: document.getElementById( 'sio-progress-wrap' ),
			bar: document.getElementById( 'sio-progress-bar' ),
			count: document.getElementById( 'sio-progress-count' ),
			eta: document.getElementById( 'sio-progress-eta' ),
			status: document.getElementById( 'sio-status' ),
			log: document.getElementById( 'sio-log' )
		};

		if ( ! el.start ) {
			return;
		}

		el.start.addEventListener( 'click', start );

		el.pause.addEventListener( 'click', function () {
			if ( ! state.running ) {
				return;
			}
			state.paused = true;
			toggleButtons();
			setStatus( i18n.paused || 'Paused.' );
		} );

		el.resume.addEventListener( 'click', function () {
			if ( ! state.running || ! state.paused ) {
				return;
			}
			state.paused = false;
			toggleButtons();
			setStatus( i18n.resumed || 'Resuming...' );
			processNext();
		} );

		el.cancel.addEventListener( 'click', function () {
			if ( ! state.running ) {
				return;
			}
			state.cancelled = true;
			state.paused = false;
		} );
	} );
} )();
