<?php
/**
 * Admin View: Notice - Currency not supported.
 *
 * @package WooCommerce_PagSeguro/Admin/Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error inline">
	<p><strong><?php esc_html_e( 'PagSeguro Disabled', 'woocommerce-pagseguro' ); ?></strong>: <?php printf( esc_html__( 'Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'woocommerce-pagseguro' ), esc_html( get_woocommerce_currency() ) ); ?>
	</p>
</div>
