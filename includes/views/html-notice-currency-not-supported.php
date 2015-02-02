<?php
/**
 * Admin View: Notice - Currency not supported.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error">
	<p><strong><?php _e( 'PagSeguro Disabled', 'woocommerce-pagseguro' ); ?></strong>: <?php printf( __( 'Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'woocommerce-pagseguro' ), get_woocommerce_currency() ); ?>
	</p>
</div>
