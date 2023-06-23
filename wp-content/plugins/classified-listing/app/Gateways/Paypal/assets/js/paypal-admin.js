;( function( $ ) {
	'use strict';

	/**
	 * Object to handle PayPal admin functions.
	 */
	var rtcl_paypal_admin = {
		isTestMode: function() {
			return $( '#rtcl_payment_paypal-testmode' ).is( ':checked' );
		},

		/**
		 * Initialize.
		 */
		init: function() {
			$( document.body ).on( 'change', '#rtcl_payment_paypal-testmode', function() {

				var test_api_username = $( '#rtcl_payment_paypal-sandbox_api_username' ).parents( 'tr' ).eq( 0 ),
					test_api_password = $( '#rtcl_payment_paypal-sandbox_api_password' ).parents( 'tr' ).eq( 0 ),
					test_api_signature = $( '#rtcl_payment_paypal-sandbox_api_signature' ).parents( 'tr' ).eq( 0 ),
					live_api_username = $( '#rtcl_payment_paypal-api_username' ).parents( 'tr' ).eq( 0 ),
					live_api_password = $( '#rtcl_payment_paypal-api_password' ).parents( 'tr' ).eq( 0 ),
					live_api_signature = $( '#rtcl_payment_paypal-api_signature' ).parents( 'tr' ).eq( 0 );

				if ( $( this ).is( ':checked' ) ) {
					test_api_username.show();
					test_api_password.show();
					test_api_signature.show();
					live_api_username.hide();
					live_api_password.hide();
					live_api_signature.hide();
				} else {
					test_api_username.hide();
					test_api_password.hide();
					test_api_signature.hide();
					live_api_username.show();
					live_api_password.show();
					live_api_signature.show();
				}
			} );

			$( '#rtcl_payment_paypal-testmode' ).change();
		}
	};

	rtcl_paypal_admin.init();
})(jQuery);
