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
	<p>
		<strong><?php esc_html_e( 'PagSeguro Disabled', 'woocommerce-pagseguro' ); ?></strong>:
		<span> </span>
		<?php
			echo wp_kses(
				sprintf(
					/* translators: %s: currency code */
					__( 'Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'woocommerce-pagseguro' ),
					get_woocommerce_currency()
				),
				array(
					'code' => array(),
				)
			);
			?>
	</p>
</div>
