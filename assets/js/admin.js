/**
 * Smart Image Optimizer - settings page enhancements.
 * Vanilla JS, no dependencies. Lazy-loaded in the footer.
 */
( function () {
	'use strict';

	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	ready( function () {
		// Live range value output.
		var ranges = document.querySelectorAll( '[data-sio-range]' );
		Array.prototype.forEach.call( ranges, function ( range ) {
			var targetId = range.getAttribute( 'data-sio-range' );
			var output = document.getElementById( targetId );
			if ( ! output ) {
				return;
			}
			range.addEventListener( 'input', function () {
				output.textContent = range.value;
			} );
		} );
	} );
} )();
