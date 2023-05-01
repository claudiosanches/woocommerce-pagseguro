<?php
/**
 * Admin View: Notice - Token missing
 *
 * @package WooCommerce_PagSeguro/Admin/Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error inline">
	<p><strong><?php esc_html_e( 'PagSeguro Disabled', 'woocommerce-pagseguro' ); ?></strong>: <?php esc_html_e( 'You should inform your token.', 'woocommerce-pagseguro' ); ?>
	</p>
</div>
