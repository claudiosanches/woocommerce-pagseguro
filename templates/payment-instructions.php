<?php
/**
 * Payment instructions.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_PagSeguro/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php if ( 2 === $type ) : ?>

	<div class="woocommerce-message">
		<span><a class="button" href="<?php echo esc_url( $link ); ?>" target="_blank"><?php esc_html_e( 'Pay the Banking Ticket', 'woocommerce-pagseguro' ); ?></a><?php esc_html_e( 'Please click in the following button to view your Banking Ticket.', 'woocommerce-pagseguro' ); ?><br /><?php esc_html_e( 'You can print and pay in your internet banking or in a lottery retailer.', 'woocommerce-pagseguro' ); ?><br /><?php esc_html_e( 'After we receive the ticket payment confirmation, your order will be processed.', 'woocommerce-pagseguro' ); ?></span>
	</div>

<?php elseif ( 3 === $type ) : ?>

	<div class="woocommerce-message">
		<span><a class="button" href="<?php echo esc_url( $link ); ?>" target="_blank"><?php esc_html_e( 'Pay at your bank', 'woocommerce-pagseguro' ); ?></a><?php esc_html_e( 'Please use the following button to make the payment in your bankline.', 'woocommerce-pagseguro' ); ?><br /><?php esc_html_e( 'After we receive the confirmation from the bank, your order will be processed.', 'woocommerce-pagseguro' ); ?></span>
	</div>

<?php else : ?>

	<div class="woocommerce-message">
		<?php // translators: %1$s for number of installments, %2$s for payment method. ?>
		<span><?php echo sprintf( esc_html__( 'You just made the payment in %1$s using the %2$s.', 'woocommerce-pagseguro' ), '<strong>' . esc_html( $installments ) . 'x</strong>', '<strong>' . esc_html( $method ) . '</strong>' ); ?><br /><?php esc_html_e( 'As soon as the credit card operator confirm the payment, your order will be processed.', 'woocommerce-pagseguro' ); ?></span>
	</div>

	<?php
endif;
