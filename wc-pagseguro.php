<?php
/**
 * Plugin Name: WooCommerce PagSeguro
 * Plugin URI: http://github.com/claudiosmweb/woocommerce-pagseguro
 * Description: Gateway de pagamento PagSeguro para WooCommerce.
 * Author: claudiosanches, Gabriel Reguly
 * Author URI: http://claudiosmweb.com/
 * Version: 1.5.0
 * License: GPLv2 or later
 * Text Domain: wcpagseguro
 * Domain Path: /languages/
 */

/**
 * WooCommerce fallback notice.
 */
function wcpagseguro_woocommerce_fallback_notice() {
    $html = '<div class="error">';
        $html .= '<p>' . __( 'WooCommerce PagSeguro Gateway depends on the last version of <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!', 'wcpagseguro' ) . '</p>';
    $html .= '</div>';

    echo $html;
}

/**
 * Load functions.
 */
function wcpagseguro_gateway_load() {

    // Checks with WooCommerce is installed.
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        add_action( 'admin_notices', 'wcpagseguro_woocommerce_fallback_notice' );

        return;
    }

    /**
     * Load textdomain.
     */
    load_plugin_textdomain( 'wcpagseguro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * Add the gateway to WooCommerce.
     *
     * @param  array $methods WooCommerce payment methods.
     *
     * @return array          Payment methods with PagSeguro.
     */
    function wcpagseguro_add_gateway( $methods ) {
        $methods[] = 'WC_PagSeguro_Gateway';

        return $methods;
    }

    add_filter( 'woocommerce_payment_gateways', 'wcpagseguro_add_gateway' );

    // Include the WC_PagSeguro_Gateway class.
    require_once plugin_dir_path( __FILE__ ) . 'class-wc-pagseguro-gateway.php';
}

add_action( 'plugins_loaded', 'wcpagseguro_gateway_load', 0 );

/**
 * Adds support to legacy IPN.
 *
 * @return void
 */
function wcpagseguro_legacy_ipn() {
    if ( isset( $_POST['Referencia'] ) && ! isset( $_GET['wc-api'] ) ) {
        global $woocommerce;

        $woocommerce->payment_gateways();

        do_action( 'woocommerce_api_wc_pagseguro_gateway' );
    }
}

add_action( 'init', 'wcpagseguro_legacy_ipn' );

/**
 * Hides the PagSeguro with payment method with the customer lives outside Brazil
 *
 * @param  array $available_gateways Default Available Gateways.
 *
 * @return array                    New Available Gateways.
 */
function wcpagseguro_hides_when_is_outside_brazil( $available_gateways ) {

    // Remove standard shipping option.
    if ( isset( $_REQUEST['country'] ) && 'BR' != $_REQUEST['country'] )
        unset( $available_gateways['pagseguro'] );

    return $available_gateways;
}

add_filter( 'woocommerce_available_payment_gateways', 'wcpagseguro_hides_when_is_outside_brazil' );

/**
 * Adds custom settings url in plugins page.
 *
 * @param  array $links Default links.
 *
 * @return array        Default links and settings link.
 */
function wcpagseguro_action_links( $links ) {

    $settings = array(
        'settings' => sprintf(
            '<a href="%s">%s</a>',
            admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_PagSeguro_Gateway' ),
            __( 'Settings', 'wcpagseguro' )
        )
    );

    return array_merge( $settings, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wcpagseguro_action_links' );
