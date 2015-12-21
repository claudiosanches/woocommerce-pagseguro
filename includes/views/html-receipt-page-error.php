<?php
/**
 * Receipt page error template
 *
 * @package WooCommerce_PagSeguro/Templates
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<ul class="woocommerce-error">
	<?php foreach ( $response['error'] as $message ) : ?>
		<li><?php echo $message; ?></li>
	<?php endforeach; ?>
</ul>

<a class="button cancel" href="<?php echo esc_url( $order->get_cancel_order_url() ); ?>"><?php esc_html_e( 'Click to try again', 'woocommerce-pagseguro' ); ?></a>
