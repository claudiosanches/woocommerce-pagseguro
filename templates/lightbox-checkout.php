<?php
/**
 * Lightbox checkout.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_PagSeguro/Templates
 * @version 2.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<p id="browser-has-javascript" style="display: none;"><?php _e( 'Thank you for your order, please wait a few seconds to make the payment with PagSeguro.', 'woocommerce-pagseguro' ); ?></p>

<p id="browser-no-has-javascript"><?php _e( 'Thank you for your order, please click the button below to pay with PagSeguro.', 'woocommerce-pagseguro' ); ?></p>

<a class="button cancel" id="cancel-payment" href="<?php echo esc_url( $cancel_order_url ); ?>"><?php _e( 'Cancel order &amp; restore cart', 'woocommerce-pagseguro' ); ?></a> <a id="submit-payment" class="button alt" href="<?php echo esc_url( $payment_url ); ?>"><?php _e( 'Pay via PagSeguro', 'woocommerce-pagseguro' ); ?></a>

<script type="text/javascript" src="<?php echo esc_url( $lightbox_script_url ); ?>"></script>
