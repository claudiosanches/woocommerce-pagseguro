(function ( $ ) {
	'use strict';

	$( function () {

		/**
		 * Switch transparent checkout options display basead in payment type.
		 *
		 * @param {String} method
		 */
		function pagSeguroSwitchTCOptions( method ) {
			var fields  = $( '#woocommerce_pagseguro_tc_credit' ).closest( '.form-table' ),
				heading = fields.prev( 'h3' );

			if ( 'transparent' === method ) {
				fields.show();
				heading.show();
			} else {
				fields.hide();
				heading.hide();
			}
		}

		/**
		 * Switch banking ticket message display.
		 *
		 * @param {String} checked
		 */
		function pagSeguroSwitchOptions( checked ) {
			var fields = $( '#woocommerce_pagseguro_tc_ticket_message' ).closest( 'tr' );

			if ( checked ) {
				fields.show();
			} else {
				fields.hide();
			}
		}

		/**
		 * Awitch user data for sandbox and production.
		 *
		 * @param {String} checked
		 */
		function pagSeguroSwitchUserData( checked ) {
			var email = $( '#woocommerce_pagseguro_email' ).closest( 'tr' ),
				token = $( '#woocommerce_pagseguro_token' ).closest( 'tr' ),
				sandboxEmail = $( '#woocommerce_pagseguro_sandbox_email' ).closest( 'tr' ),
				sandboxToken = $( '#woocommerce_pagseguro_sandbox_token' ).closest( 'tr' );

			if ( checked ) {
				email.hide();
				token.hide();
				sandboxEmail.show();
				sandboxToken.show();
			} else {
				email.show();
				token.show();
				sandboxEmail.hide();
				sandboxToken.hide();
			}
		}

		$( 'body' ).on( 'change', '#woocommerce_pagseguro_method', function () {
			pagSeguroSwitchTCOptions( $( this ).val() );
		}).change();

		pagSeguroSwitchOptions( $( '#woocommerce_pagseguro_tc_ticket' ).is( ':checked' ) );
		$( 'body' ).on( 'change', '#woocommerce_pagseguro_tc_ticket', function () {
			pagSeguroSwitchOptions( $( this ).is( ':checked' ) );
		});

		pagSeguroSwitchUserData( $( '#woocommerce_pagseguro_sandbox' ).is( ':checked' ) );
		$( 'body' ).on( 'change', '#woocommerce_pagseguro_sandbox', function () {
			pagSeguroSwitchUserData( $( this ).is( ':checked' ) );
		});
	});

}( jQuery ));
