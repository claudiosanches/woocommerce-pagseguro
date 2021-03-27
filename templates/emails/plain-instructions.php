<?php
/**
 * Plain email instructions.
 *
 * @author  Claudio_Sanches
 * @package WooCommerce_PagSeguro/Templates
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

esc_html_e( 'Payment', 'woocommerce-pagseguro' );

echo "\n\n";

if ( 2 === $type ) {

	esc_html_e( 'Please use the link below to view your Banking Ticket, you can print and pay in your internet banking or in a lottery retailer:', 'woocommerce-pagseguro' );

	echo "\n";

	echo esc_url( $link );

	echo "\n";

	esc_html_e( 'After we receive the ticket payment confirmation, your order will be processed.', 'woocommerce-pagseguro' );

} elseif ( 3 === $type ) {

	esc_html_e( 'Please use the link below to make the payment in your bankline:', 'woocommerce-pagseguro' );

	echo "\n";

	echo esc_url( $link );

	echo "\n";

	esc_html_e( 'After we receive the confirmation from the bank, your order will be processed.', 'woocommerce-pagseguro' );

} else {

	echo sprintf( esc_html__( 'You just made the payment in %1$s using the %2$s.', 'woocommerce-pagseguro' ), esc_html( $installments ) . 'x', esc_html( $method ) );

	echo "\n";

	esc_html_e( 'As soon as the credit card operator confirm the payment, your order will be processed.', 'woocommerce-pagseguro' );

}

echo "\n\n****************************************************\n\n";
