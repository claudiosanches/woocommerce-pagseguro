<?php
/**
 * HTML email instructions.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_PagSeguro/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<h2><?php esc_html_e( 'Payment', 'woocommerce-pagseguro' ); ?></h2>

<?php if ( 2 === $type ) : ?>

	<p class="order_details"><?php esc_html_e( 'Please use the link below to view your Banking Ticket, you can print and pay in your internet banking or in a lottery retailer:', 'woocommerce-pagseguro' ); ?><br /><a class="button" href="<?php echo esc_url( $link ); ?>" target="_blank"><?php esc_html_e( 'Pay the Banking Ticket', 'woocommerce-pagseguro' ); ?></a><br /><?php esc_html_e( 'After we receive the ticket payment confirmation, your order will be processed.', 'woocommerce-pagseguro' ); ?></p>

<?php elseif ( 3 === $type ) : ?>

	<p class="order_details"><?php esc_html_e( 'Please use the link below to make the payment in your bankline:', 'woocommerce-pagseguro' ); ?><br /><a class="button" href="<?php echo esc_url( $link ); ?>" target="_blank"><?php esc_html_e( 'Pay at your bank', 'woocommerce-pagseguro' ); ?>.<br /><?php esc_html_e( 'After we receive the confirmation from the bank, your order will be processed.', 'woocommerce-pagseguro' ); ?></a></p>

<?php else : ?>

	<?php // translators: %1$s for number of installments, %2$s for payment method. ?>
	<p class="order_details"><?php echo sprintf( esc_html__( 'You just made the payment in %1$s using the %2$s.', 'woocommerce-pagseguro' ), '<strong>' . esc_html( $installments ) . 'x</strong>', '<strong>' . esc_html( $method ) . '</strong>' ); ?><br /><?php esc_html_e( 'As soon as the credit card operator confirm the payment, your order will be processed.', 'woocommerce-pagseguro' ); ?></p>

	<?php
endif;
