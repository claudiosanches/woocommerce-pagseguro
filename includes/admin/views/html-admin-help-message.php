<?php
/**
 * Admin help message.
 *
 * @package WooCommerce_PagSeguro/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( apply_filters( 'woocommerce_pagseguro_help_message', true ) ) : ?>
	<div class="updated inline woocommerce-message">
		<p><?php echo esc_html( sprintf( __( 'Help us keep the %s plugin free making a donation or rate %s on WordPress.org. Thank you in advance!', 'woocommerce-pagseguro' ), __( 'Claudio Sanches - PagSeguro for WooCommerce', 'woocommerce-pagseguro' ), '&#9733;&#9733;&#9733;&#9733;&#9733;' ) ); ?></p>
		<p><a href="http://claudiosmweb.com/doacoes/" target="_blank" class="button button-primary"><?php esc_html_e( 'Make a donation', 'woocommerce-pagseguro' ); ?></a> <a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-pagseguro?filter=5#postform" target="_blank" class="button button-secondary"><?php esc_html_e( 'Make a review', 'woocommerce-pagseguro' ); ?></a></p>
	</div>
<?php endif;
