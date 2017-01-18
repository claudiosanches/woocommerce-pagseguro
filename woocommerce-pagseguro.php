<?php
/**
 * Plugin Name: WooCommerce PagSeguro
 * Plugin URI: http://github.com/claudiosmweb/woocommerce-pagseguro
 * Description: Gateway de pagamento PagSeguro para WooCommerce.
 * Author: Claudio Sanches
 * Author URI: http://claudiosmweb.com/
 * Version: 2.11.5
 * License: GPLv2 or later
 * Text Domain: woocommerce-pagseguro
 * Domain Path: languages/
 *
 * @package WooCommerce_PagSeguro
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
		const VERSION = '2.11.5';

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
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Payment_Gateway' ) ) {
				$this->includes();

				add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
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
		 * Get main file.
		 *
		 * @return string
		 */
		public static function get_main_file() {
			return __FILE__;
		}

		/**
		 * Get plugin path.
		 *
		 * @return string
		 */
		public static function get_plugin_path() {
			return plugin_dir_path( __FILE__ );
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return self::get_plugin_path() . 'templates/';
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'woocommerce-pagseguro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Includes.
		 */
		private function includes() {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '>=' ) ) {

			} else {
				include_once dirname( __FILE__ ) . '/includes/legacy/class-wc-pagseguro-legacy-actions.php';
				include_once dirname( __FILE__ ) . '/includes/legacy/class-wc-pagseguro-xml.php';
				include_once dirname( __FILE__ ) . '/includes/legacy/class-wc-pagseguro-api.php';
				include_once dirname( __FILE__ ) . '/includes/legacy/class-wc-pagseguro-gateway.php';
			}
		}

		/**
		 * Add the gateway to WooCommerce.
		 *
		 * @param  array $methods WooCommerce payment methods.
		 *
		 * @return array          Payment methods with PagSeguro.
		 */
		public function add_gateway( $methods ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '>=' ) ) {

			} else {
				$methods[] = 'WC_PagSeguro_Gateway';
			}

			return $methods;
		}

		/**
		 * WooCommerce missing notice.
		 */
		public function woocommerce_missing_notice() {
			include dirname( __FILE__ ) . '/includes/admin/views/html-notice-missing-woocommerce.php';
		}

		/**
		 * Action links.
		 *
		 * @param array $links Action links.
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array();

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=pagseguro' ) ) . '">' . __( 'Settings', 'woocommerce-pagseguro' ) . '</a>';
			} else {
				$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_pagseguro_gateway' ) ) . '">' . __( 'Settings', 'woocommerce-pagseguro' ) . '</a>';
			}

			return array_merge( $plugin_links, $links );
		}
	}

	add_action( 'plugins_loaded', array( 'WC_PagSeguro', 'get_instance' ) );

endif;
