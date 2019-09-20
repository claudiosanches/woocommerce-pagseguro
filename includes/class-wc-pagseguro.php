<?php
/**
 * Plugin's main class
 *
 * @package WooCommerce_PagSeguro
 */

/**
 * WooCommerce bootstrap class.
 */
class WC_PagSeguro {

	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		// Load plugin text domain.
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			self::includes();

			add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'add_gateway' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'hides_when_is_outside_brazil' ) );
			add_filter( 'woocommerce_billing_fields', array( __CLASS__, 'transparent_checkout_billing_fields' ), 9999 );
			add_filter( 'woocommerce_shipping_fields', array( __CLASS__, 'transparent_checkout_shipping_fields' ), 9999 );
			add_filter( 'plugin_action_links_' . plugin_basename( WC_PAGSEGURO_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );

			if ( is_admin() ) {
				add_action( 'admin_notices', array( __CLASS__, 'ecfb_missing_notice' ) );
			}
		} else {
			add_action( 'admin_notices', array( __CLASS__, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Get templates path.
	 *
	 * @return string
	 */
	public static function get_templates_path() {
		return plugin_dir_path( WC_PAGSEGURO_PLUGIN_FILE ) . 'templates/';
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-pagseguro', false, dirname( plugin_basename( WC_PAGSEGURO_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Action links.
	 *
	 * @param array $links Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=pagseguro' ) ) . '">' . __( 'Settings', 'woocommerce-pagseguro' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Includes.
	 */
	private static function includes() {
		include_once dirname( __FILE__ ) . '/class-wc-pagseguro-xml.php';
		include_once dirname( __FILE__ ) . '/class-wc-pagseguro-api.php';
		include_once dirname( __FILE__ ) . '/class-wc-pagseguro-gateway.php';
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 *
	 * @return array          Payment methods with PagSeguro.
	 */
	public static function add_gateway( $methods ) {
		$methods[] = 'WC_PagSeguro_Gateway';

		return $methods;
	}

	/**
	 * Hides the PagSeguro with payment method with the customer lives outside Brazil.
	 *
	 * @param   array $available_gateways Default Available Gateways.
	 *
	 * @return  array                     New Available Gateways.
	 */
	public static function hides_when_is_outside_brazil( $available_gateways ) {
		// Remove PagSeguro gateway.
		if ( isset( $_REQUEST['country'] ) && 'BR' !== $_REQUEST['country'] ) { // WPCS: input var ok, CSRF ok.
			unset( $available_gateways['pagseguro'] );
		}

		return $available_gateways;
	}

	/**
	 * Transparent checkout billing fields.
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public static function transparent_checkout_billing_fields( $fields ) {
		$settings = get_option( 'woocommerce_pagseguro_settings', array( 'method' => '' ) );

		if ( 'transparent' === $settings['method'] && class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			if ( isset( $fields['billing_neighborhood'] ) ) {
				$fields['billing_neighborhood']['required'] = true;
			}
			if ( isset( $fields['billing_number'] ) ) {
				$fields['billing_number']['required'] = true;
			}
		}

		return $fields;
	}

	/**
	 * Transparent checkout billing fields.
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public static function transparent_checkout_shipping_fields( $fields ) {
		$settings = get_option( 'woocommerce_pagseguro_settings', array( 'method' => '' ) );

		if ( 'transparent' === $settings['method'] && class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			if ( isset( $fields['shipping_neighborhood'] ) ) {
				$fields['shipping_neighborhood']['required'] = true;
			}
		}

		return $fields;
	}

	/**
	 * WooCommerce Extra Checkout Fields for Brazil notice.
	 */
	public static function ecfb_missing_notice() {
		$settings = get_option( 'woocommerce_pagseguro_settings', array( 'method' => '' ) );

		if ( 'transparent' === $settings['method'] && ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			include dirname( __FILE__ ) . '/admin/views/html-notice-missing-ecfb.php';
		}
	}

	/**
	 * WooCommerce missing notice.
	 */
	public static function woocommerce_missing_notice() {
		include dirname( __FILE__ ) . '/admin/views/html-notice-missing-woocommerce.php';
	}
}
