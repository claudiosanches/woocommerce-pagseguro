<?php
/**
 * WooCommerce PagSeguro API class
 *
 * @deprecated 3.0.0
 * @package    WooCommerce_PagSeguro/Legacy/Actions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy actions.
 */
class WC_PagSeguro_Legacy_Actions {

	/**
	 * Initialize actions.
	 */
	public function __construct() {
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hides_when_is_outside_brazil' ) );
		add_filter( 'woocommerce_cancel_unpaid_order', array( $this, 'stop_cancel_unpaid_orders' ), 10, 2 );

		if ( is_admin() ) {
			add_action( 'admin_notices', array( $this, 'ecfb_missing_notice' ) );
		}
	}

	/**
	 * Hides the PagSeguro with payment method with the customer lives outside Brazil.
	 *
	 * @param   array $available_gateways Default Available Gateways.
	 *
	 * @return  array                     New Available Gateways.
	 */
	public function hides_when_is_outside_brazil( $available_gateways ) {
		// Remove PagSeguro gateway.
		if ( isset( $_REQUEST['country'], $available_gateways['pagseguro'] ) && 'BR' !== $_REQUEST['country'] ) {
			unset( $available_gateways['pagseguro'] );
		}

		return $available_gateways;
	}

	/**
	 * Stop cancel unpaid PagSeguro orders.
	 *
	 * @param  bool     $cancel Check if need cancel the order.
	 * @param  WC_Order $order  Order object.
	 *
	 * @return bool
	 */
	public function stop_cancel_unpaid_orders( $cancel, $order ) {
		if ( 'pagseguro' === $order->payment_method ) {
			return false;
		}

		return $cancel;
	}

	/**
	 * WooCommerce Extra Checkout Fields for Brazil notice.
	 */
	public function ecfb_missing_notice() {
		$settings = get_option( 'woocommerce_pagseguro_settings', array( 'method' => '' ) );

		if ( 'transparent' === $settings['method'] && ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			include dirname( __FILE__ ) . '/includes/admin/views/html-notice-missing-ecfb.php';
		}
	}
}

new WC_PagSeguro_Legacy_Actions;
