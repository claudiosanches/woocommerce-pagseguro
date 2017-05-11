<?php
/**
 * Plugin Name: WooCommerce PagSeguro
 * Plugin URI:  https://github.com/claudiosanches/woocommerce-pagseguro
 * Description: Includes PagSeguro as a payment gateway to WooCommerce.
 * Author:      Claudio Sanches
 * Author URI:  https://claudiosanches.com
 * Version:     2.12.5
 * License:     GPLv2 or later
 * Text Domain: woocommerce-pagseguro
 * Domain Path: /languages
 *
 * WooCommerce PagSeguro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WooCommerce PagSeguro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WooCommerce PagSeguro. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
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
		const VERSION = '2.12.5';

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
				add_filter( 'woocommerce_available_payment_gateways', array( $this, 'hides_when_is_outside_brazil' ) );
				add_filter( 'woocommerce_cancel_unpaid_order', array( $this, 'stop_cancel_unpaid_orders' ), 10, 2 );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

				if ( is_admin() ) {
					add_action( 'admin_notices', array( $this, 'ecfb_missing_notice' ) );
				}
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
			if ( null === self::$instance ) {
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
		 * @param array $links Action links.
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links   = array();
			$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=pagseguro' ) ) . '">' . __( 'Settings', 'woocommerce-pagseguro' ) . '</a>';

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Includes.
		 */
		private function includes() {
			include_once dirname( __FILE__ ) . '/includes/class-wc-pagseguro-xml.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-pagseguro-api.php';
			include_once dirname( __FILE__ ) . '/includes/class-wc-pagseguro-gateway.php';
		}

		/**
		 * Add the gateway to WooCommerce.
		 *
		 * @param  array $methods WooCommerce payment methods.
		 *
		 * @return array          Payment methods with PagSeguro.
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
			if ( isset( $_REQUEST['country'] ) && 'BR' !== $_REQUEST['country'] ) {
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
			$payment_method = method_exists( $order, 'get_payment_method' ) ? $order->get_payment_method() : $order->payment_method;

			if ( 'pagseguro' === $payment_method ) {
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

		/**
		 * WooCommerce missing notice.
		 */
		public function woocommerce_missing_notice() {
			include dirname( __FILE__ ) . '/includes/admin/views/html-notice-missing-woocommerce.php';
		}
	}

	add_action( 'plugins_loaded', array( 'WC_PagSeguro', 'get_instance' ) );

endif;
