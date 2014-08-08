<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<p id="browser-has-javascript" style="display: none;"><?php _e( 'Thank you for your order, please wait a few seconds to make the payment with PagSeguro.', 'woocommerce-pagseguro' ); ?></p>

<p id="browser-no-has-javascript"><?php _e( 'Thank you for your order, please click the button below to pay with PagSeguro.', 'woocommerce-pagseguro' ); ?></p>

<a class="button cancel" id="cancel-payment" href="<?php echo esc_url( $order->get_cancel_order_url() ); ?>"><?php _e( 'Cancel order &amp; restore cart', 'woocommerce-pagseguro' ); ?></a> <a id="submit-payment" class="button alt" href="<?php esc_url_raw( $response['url'] ); ?>"><?php _e( 'Pay via PagSeguro', 'woocommerce-pagseguro' ); ?></a>

<script type="text/javascript" src="<?php echo $this->api->get_lightbox_url(); ?>"></script>
