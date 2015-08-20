<?php
/**
 * Plugin Name: WooCommerce PagSeguro
 * Plugin URI: http://github.com/claudiosmweb/woocommerce-pagseguro
 * Description: Gateway de pagamento PagSeguro para WooCommerce.
 * Author: Claudio Sanches, Gabriel Reguly
 * Author URI: http://claudiosmweb.com/
 * Version: 2.10.3
 * License: GPLv2 or later
 * Text Domain: woocommerce-pagseguro
 * Domain Path: languages/
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
	 * @var string
	 */
	const VERSION = '2.10.3';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin public actions.
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			$this->includes();

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hides_when_is_outside_brazil' ) );
			add_filter( 'woocommerce_cancel_unpaid_order', array( $this, 'stop_cancel_unpaid_orders' ), 10, 2 );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
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
	 * Get templates path.
	 *
	 * @return string
	 */
	public static function get_templates_path() {
		return plugin_dir_path( __FILE__ ) . 'templates/';
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-pagseguro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Action links.
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array();

		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_pagseguro_gateway' ) ) . '">' . __( 'Settings', 'woocommerce-pagseguro' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Includes.
	 */
	private function includes() {
		include_once 'includes/class-wc-pagseguro-xml.php';
		include_once 'includes/class-wc-pagseguro-api.php';
		include_once 'includes/class-wc-pagseguro-gateway.php';
	}

	/**
	 * Add the gateway to WooCommerce.
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
	 * Hides the PagSeguro with payment method with the customer lives outside Brazil.
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

	/**
	 * Stop cancel unpaid PagSeguro orders.
	 *
	 * @param  bool     $cancel
	 * @param  WC_Order $order
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
	 * WooCommerce fallback notice.
	 *
	 * @return  string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce PagSeguro Gateway depends on the last version of %s to work!', 'woocommerce-pagseguro' ), '<a href="http://wordpress.org/plugins/woocommerce/">' . __( 'WooCommerce', 'woocommerce-pagseguro' ) . '</a>' ) . '</p></div>';
	}
}

add_action( 'plugins_loaded', array( 'WC_PagSeguro', 'get_instance' ) );

endif;
