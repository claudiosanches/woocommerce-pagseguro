<?php
/**
 * Plugin Name:          WooCommerce PagSeguro
 * Plugin URI:           https://github.com/claudiosanches/woocommerce-pagseguro
 * Description:          Includes PagSeguro as a payment gateway to WooCommerce.
 * Author:               Claudio Sanches
 * Author URI:           https://claudiosanches.com
 * Version:              2.13.1
 * License:              GPLv3 or later
 * Text Domain:          woocommerce-pagseguro
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      3.4.0
 *
 * WooCommerce PagSeguro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * WooCommerce PagSeguro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WooCommerce PagSeguro. If not, see
 * <https://www.gnu.org/licenses/gpl-3.0.txt>.
 *
 * @package WooCommerce_PagSeguro
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants.
define( 'WC_PAGSEGURO_VERSION', '2.13.1' );
define( 'WC_PAGSEGURO_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_PagSeguro' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-pagseguro.php';
	add_action( 'plugins_loaded', array( 'WC_PagSeguro', 'init' ) );
}
