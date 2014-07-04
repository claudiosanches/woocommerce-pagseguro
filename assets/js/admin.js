(function ( $ ) {
	'use strict';

	$( function () {

		/**
		 * Switch transparent checkout options display basead in payment type.
		 *
		 * @param  {string} method
		 */
		function pagSeguroSwitchTCOptions( method ) {
			var fields = $( '#mainform h4:eq(0), #mainform .form-table:eq(1)' );

			if ( 'transparent' === method ) {
				fields.show();
			} else {
				fields.hide();
			}
		}

		/**
		 * Switch banking ticket message display.
		 *
		 * @param  {string} checked
		 */
		function pagSeguroSwitchOptions( checked ) {
			var fields = $( '#mainform .form-table:eq(1) tr:eq(3)' );

			if ( checked ) {
				fields.show();
			} else {
				fields.hide();
			}
		}

		pagSeguroSwitchTCOptions( $( '#woocommerce_pagseguro_method option:selected' ).val() );

		$( 'body' ).on( 'change', '#woocommerce_pagseguro_method', function () {
			pagSeguroSwitchTCOptions( $( this ).val() );
		});

		pagSeguroSwitchOptions( $( '#woocommerce_pagseguro_tc_ticket' ).is( ':checked' ) );

		$( 'body' ).on( 'click', '#woocommerce_pagseguro_tc_ticket', function () {
			pagSeguroSwitchOptions( $( this ).is( ':checked' ) );
		});
	});

}( jQuery ));
