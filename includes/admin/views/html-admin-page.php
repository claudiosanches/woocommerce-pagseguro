<?php
/**
 * Admin options screen.
 *
 * @package WooCommerce_PagSeguro/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews_url = 'https://wordpress.org/support/view/plugin-reviews/woocommerce-pagseguro?filter=5#postform';

?>

<h3><?php echo esc_html( $this->method_title ); ?></h3>

<?php
	if ( 'yes' == $this->get_option( 'enabled' ) ) {
		if ( ! $this->using_supported_currency() && ! class_exists( 'woocommerce_wpml' ) ) {
			include 'html-notice-currency-not-supported.php';
		}

		if ( empty( $this->get_email() ) ) {
			include_once 'html-notice-email-missing.php';
		}

		if ( empty( $this->get_token() ) ) {
			include_once 'html-notice-token-missing.php';
		}
	}
?>

<?php echo wpautop( $this->method_description ); ?>

<?php include 'html-admin-help-message.php'; ?>

<table class="form-table">
	<?php $this->generate_settings_html(); ?>
</table>
