<?php
/**
 * Plugin Name: WooCommerce PagSeguro
 * Plugin URI: http://github.com/claudiosmweb/woocommerce-pagseguro
 * Description: Gateway de pagamento PagSeguro para WooCommerce.
 * Author: claudiosanches, Gabriel Reguly
 * Author URI: http://claudiosmweb.com/
 * Version: 2.3.0
 * License: GPLv2 or later
 * Text Domain: woocommerce-pagseguro
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_PagSeguro' ) ) :

/**
 * WooCommerce PagSeguro main class.
 */
class WC_PagSeguro {

	/**
	 * Plugin version.
	 *
	 * @since 2.3.0
	 *
	 * @var   string
	 */
	const VERSION = '2.3.0';

	/**
	 * Integration id.
	 *
	 * @since 2.3.0
	 *
	 * @var   string
	 */
	protected static $gateway_id = 'pagseguro';

	/**
	 * Plugin slug.
	 *
	 * @since 2.3.0
	 *
	 * @var   string
	 */
	protected static $plugin_slug = 'woocommerce-pagseguro';

	/**
	 * Instance of this class.
	 *
	 * @since 2.3.0
	 *
	 * @var   object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin public actions.
	 *
	 * @since  2.3.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			// Include the WC_PagSeguro_Gateway class.
			include_once 'includes/class-wc-pagseguro-gateway.php';

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hides_when_is_outside_brazil' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since  2.3.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since  2.3.0
	 *
	 * @return string Plugin slug variable.
	 */
	public static function get_plugin_slug() {
		return self::$plugin_slug;
	}

	/**
	 * Return the gateway id/slug.
	 *
	 * @since  2.3.0
	 *
	 * @return string Gateway id/slug variable.
	 */
	public static function get_gateway_id() {
		return self::$gateway_id;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  2.3.0
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$domain = self::$plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @version 2.3.0
	 *
	 * @param   array $methods WooCommerce payment methods.
	 *
	 * @return  array          Payment methods with PagSeguro.
	 */
	public function add_gateway( $methods ) {
		$methods[] = 'WC_PagSeguro_Gateway';

		return $methods;
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @version 2.3.0
	 *
	 * @return  string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce PagSeguro Gateway depends on the last version of %s to work!', self::$plugin_slug ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', self::$plugin_slug ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Hides the PagSeguro with payment method with the customer lives outside Brazil.
	 *
	 * @version 2.3.0
	 *
	 * @param   array $available_gateways Default Available Gateways.
	 *
	 * @return  array                     New Available Gateways.
	 */
	public function hides_when_is_outside_brazil( $available_gateways ) {

		// Remove PagSeguro gateway.
		if ( isset( $_REQUEST['country'] ) && 'BR' != $_REQUEST['country'] ) {
			unset( $available_gateways['pagseguro'] );
		}

		return $available_gateways;
	}
}

add_action( 'plugins_loaded', array( 'WC_PagSeguro', 'get_instance' ), 0 );

endif;
