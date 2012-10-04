<?php
/**
 * Plugin Name: WooCommerce PagSeguro
 * Plugin URI: http://claudiosmweb.com/plugins/pagseguro-para-woocommerce/
 * Description: Gateway de pagamento PagSeguro para WooCommerce.
 * Author: claudiosanches, Gabriel Reguly
 * Author URI: http://www.claudiosmweb.com/
 * Version: 1.0.1
 * License: GPLv2 or later
 * Text Domain: wcpagseguro
 * Domain Path: /languages/
 */

/**
 * WooCommerce fallback notice.
 */
function wcpagseguro_woocommerce_fallback_notice(){
    $message = '<div class="error">';
        $message .= '<p>' . __( 'WooCommerce PagSeguro Gateway depends on <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!' , 'wcpagseguro' ) . '</p>';
    $message .= '</div>';

    echo $message;
}

/**
 * Load functions.
 */
add_action( 'plugins_loaded', 'wcpagseguro_gateway_load', 0 );

function wcpagseguro_gateway_load() {

    if ( !class_exists( 'WC_Payment_Gateway' ) ) {
        add_action( 'admin_notices', 'wcpagseguro_woocommerce_fallback_notice' );

        return;
    }

    /**
     * Load textdomain.
     */
    load_plugin_textdomain( 'wcpagseguro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * Add the gateway to WooCommerce
     *
     * @access public
     * @param array $methods
     * @return array
     */
    add_filter('woocommerce_payment_gateways', 'wcpagseguro_add_gateway' );

    function wcpagseguro_add_gateway( $methods ) {
        $methods[] = 'WC_PagSeguro_Gateway';
        return $methods;
    }



    class WC_PagSeguro_Gateway extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         *
         * @access public
         * @return void
         */
        public function __construct() {
            global $woocommerce;

            $this->id            = 'pagseguro';
            $this->icon          = plugins_url( 'images/pagseguro.png', __FILE__ );
            $this->has_fields    = false;
            $this->pagseguro_url = 'https://pagseguro.uol.com.br/v2/checkout/payment.html';
            $this->method_title  = __( 'PagSeguro', 'wcpagseguro' );


            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->title            = $this->settings['title'];
            $this->description      = $this->settings['description'];
            $this->email            = $this->settings['email'];
            $this->invoice_prefix   = !empty( $this->settings['invoice_prefix'] ) ? $this->settings['invoice_prefix'] : 'WC-';

            // Actions
            add_action( 'woocommerce_receipt_pagseguro', array( &$this, 'receipt_page' ) );
            add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );

			$this->enabled = ( 'yes' == $this->settings['enabled'] ) && $this->is_valid_for_use() && !empty( $this->email );
        }


        /**
         * Check if this gateway is enabled and available in the user's country
         *
         * @access public
         * @return bool
         */
        function is_valid_for_use() {
            if ( !in_array( get_woocommerce_currency() , array( 'BRL' ) ) ) return false;
            return true;
        }

        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         *
         * @since 1.0.0
         */
        public function admin_options() {

            ?>
            <h3><?php _e( 'PagSeguro standard', 'wcpagseguro' ); ?></h3>
            <p><?php _e( 'PagSeguro standard works by sending the user to PagSeguro to enter their payment information.', 'wcpagseguro' ); ?></p>
            <table class="form-table">
            <?php
                if ( ! $this->is_valid_for_use() ) {
                    ?>
                        <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'wcpagseguro' ); ?></strong>: <?php _e( 'PagSeguro does not support your store currency.', 'wcpagseguro' ); ?></p></div>
                    <?php
                } else {
					if ( empty( $this->email ) ) {
?>
						<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'wcpagseguro' ); ?></strong>: <?php _e( 'You should inform your email address in PagSeguro.', 'wcpagseguro' ); ?></p></div>
<?php
					}
					// Generate the HTML For the settings form.
					$this->generate_settings_html();
				}
            ?>
            </table><!--/.form-table-->
            <?php
        }

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'wcpagseguro' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable PagSeguro standard', 'wcpagseguro' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Title', 'wcpagseguro' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'wcpagseguro' ),
                    'default' => __( 'PagSeguro', 'wcpagseguro' )
                ),
                'description' => array(
                    'title' => __( 'Description', 'wcpagseguro' ),
                    'type' => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'wcpagseguro' ),
                    'default' => __( 'Pay via PagSeguro', 'wcpagseguro' )
                ),
                'email' => array(
                    'title' => __( 'PagSeguro Email', 'wcpagseguro' ),
                    'type' => 'text',
                    'description' => __( 'Please enter your PagSeguro email address; this is needed in order to take payment.', 'wcpagseguro' ),
                    'default' => ''
                ),
                'invoice_prefix' => array(
                    'title' => __( 'Invoice Prefix', 'wcpagseguro' ),
                    'type' => 'text',
                    'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PagSeguro account for multiple stores ensure this prefix is unqiue as PagSeguro will not allow orders with the same invoice number.', 'wcpagseguro' ),
                    'default' => 'WC-'
                )
            );

        }

        /**
         * Get PagSeguro Args for passing to PP
         *
         * @access public
         * @param mixed $order
         * @return array
         */
        function get_pagseguro_args( $order ) {
            global $woocommerce;

            $order_id = $order->id;

            // Fixed phone number.
            $order->billing_phone = str_replace( array( '(', '-', ' ', ')' ), '', $order->billing_phone );
            $phone_args = array(
                'senderAreaCode' => substr( $order->billing_phone, 0, 2 ),
                'senderPhone' => substr( $order->billing_phone, 2 ),
            );

            // Fixed postal code.
            $order->billing_postcode = str_replace( array( '-', ' ' ), '', $order->billing_postcode );

            // Fixed Address.
            $order->billing_address_1 = explode( ',', $order->billing_address_1 );

            // Fixed PagSeguro Country.
            if ( $order->billing_country == 'BR' ) {
                $order->billing_country = 'BRA';
            }

            // PayPal Args
            $pagseguro_args = array_merge(
                array(
                    'receiverEmail'             => $this->email,
                    'currency'                  => get_woocommerce_currency(),
                    'encoding'                  => 'UTF-8',

                    // Sender info.
                    'senderName'                => $order->billing_first_name . ' ' . $order->billing_last_name,
                    'senderEmail'               => $order->billing_email,

                    // Address info.
                    'shippingAddressPostalCode' => $order->billing_postcode,
                    'shippingAddressStreet'     => $order->billing_address_1[0],
                    'shippingAddressNumber'     => (int) $order->billing_address_1[1],
                    'shippingAddressComplement' => $order->billing_address_2,
                    'shippingAddressCity'       => $order->billing_city,
                    'shippingAddressState'      => $order->billing_state,
                    'shippingAddressCountry'    => $order->billing_country,

                    // Extras.
                    'extraAmount'               => $order->get_total_tax(),

                    // Payment Info.
                    'reference'                 => $this->invoice_prefix . $order_id,
                ),
                $phone_args
            );

            // If prices include tax or have order discounts, send the whole order as a single item
            if ( get_option('woocommerce_prices_include_tax') == 'yes' || $order->get_order_discount() > 0 ) :

                // Discount.
                $pagseguro_args['extraAmount'] = $order->get_order_discount();

                // Don't pass items - pagseguro borks tax due to prices including tax.
                // PagSeguro has no option for tax inclusive pricing sadly. Pass 1 item for the order items overall.
                $item_names = array();

                if ( sizeof( $order->get_items() ) > 0 ) : foreach ( $order->get_items() as $item ) :
                    if ( $item['qty'] ) $item_names[] = $item['name'] . ' x ' . $item['qty'];
                endforeach; endif;

                $pagseguro_args['itemId1']          = 1;
                $pagseguro_args['itemDescription1'] = substr( sprintf( __( 'Order %s' , 'wcpagseguro' ), $order->get_order_number() ) . " - " . implode(', ', $item_names), 0, 110 );
                $pagseguro_args['itemQuantity1']    = 1;
                $pagseguro_args['itemAmount1']      = number_format( $order->get_total() - $order->get_shipping() - $order->get_shipping_tax() + $order->get_order_discount(), 2, '.', '' );

                if ( ( $order->get_shipping() + $order->get_shipping_tax() ) > 0 ) :
                    $pagseguro_args['itemId2'] = 2;
                    $pagseguro_args['itemDescription2'] = __( 'Shipping via', 'wcpagseguro' ) . ' ' . ucwords( $order->shipping_method_title );
                    $pagseguro_args['itemQuantity2']  = '1';
                    $pagseguro_args['itemAmount2']    = number_format( $order->get_shipping() + $order->get_shipping_tax() , 2, '.', '' );
                endif;

            else :

                // Tax
                $pagseguro_args['tax_cart'] = $order->get_total_tax();

                // Cart Contents
                $item_loop = 0;
                if ( sizeof( $order->get_items() ) >0 ) :
                    foreach ( $order->get_items() as $item ) :
                        if ( $item['qty'] ) :

                            $item_loop++;

                            $product = $order->get_product_from_item( $item );

                            $item_name  = $item['name'];

                            $item_meta = new WC_Order_Item_Meta( $item['item_meta'] );
                            if ( $meta = $item_meta->display( true, true ) ) :
                                $item_name .= ' ('.$meta.')';
                            endif;

                            $pagseguro_args['itemId' . $item_loop] = $item_loop;
                            $pagseguro_args['itemDescription' . $item_loop] = $item_name;
                            $pagseguro_args['itemQuantity' . $item_loop] = $item['qty'];
                            $pagseguro_args['itemAmount' . $item_loop] = $order->get_item_total( $item, false );

                        endif;
                    endforeach;
                endif;

                // Shipping Cost item
                if ( $order->get_shipping() > 0 ) :
                    $item_loop++;
                    $pagseguro_args['itemId' . $item_loop] = $item_loop;
                    $pagseguro_args['itemDescription' . $item_loop] = __( 'Shipping via', 'wcpagseguro' ) . ' ' . ucwords( $order->shipping_method_title );
                    $pagseguro_args['itemQuantity' . $item_loop] = '1';
                    $pagseguro_args['itemAmount' . $item_loop] = number_format( $order->get_shipping(), 2, '.', '' );
                endif;

            endif;

            $pagseguro_args = apply_filters( 'woocommerce_pagseguro_args', $pagseguro_args );

            return $pagseguro_args;
        }

        /**
         * Generate the paypal button link
         *
         * @access public
         * @param mixed $order_id
         * @return string
         */
        function generate_pagseguro_form( $order_id ) {
            global $woocommerce;

            $order = new WC_Order( $order_id );

            $pagseguro_adr = $this->pagseguro_url . '?';

            $pagseguro_args = $this->get_pagseguro_args( $order );

            $pagseguro_args_array = array();

            foreach ( $pagseguro_args as $key => $value ) {
                $pagseguro_args_array[] = '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
            }

            $woocommerce->add_inline_js( '
                jQuery("body").block({
                        message: "<img src=\"' . esc_url( $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />'.__( 'Thank you for your order. We are now redirecting you to PagSeguro to make payment.', 'wcpagseguro' ).'",
                        overlayCSS:
                        {
                            background: "#fff",
                            opacity:    0.6
                        },
                        css: {
                            padding:         20,
                            textAlign:       "center",
                            color:           "#555",
                            border:          "3px solid #aaa",
                            backgroundColor: "#fff",
                            cursor:          "wait",
                            lineHeight:      "32px",
                            zIndex:          "9999"
                        }
                    });
                jQuery("#submit_pagseguro_payment_form").click();
            ' );

            return '<form action="' . esc_url( $pagseguro_adr ) . '" method="post" id="pagseguro_payment_form" target="_top">
                    ' . implode( '', $pagseguro_args_array ) . '
                    <input type="submit" class="button-alt" id="submit_pagseguro_payment_form" value="' . __( 'Pay via PagSeguro', 'wcpagseguro' ).'" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'wcpagseguro' ) . '</a>
                </form>';

        }

        /**
         * Process the payment and return the result
         *
         * @access public
         * @param int $order_id
         * @return array
         */
        function process_payment( $order_id ) {

            $order = new WC_Order( $order_id );

            return array(
                'result'    => 'success',
                'redirect'  => add_query_arg( 'order', $order->id, add_query_arg( 'key', $order->order_key, get_permalink( woocommerce_get_page_id( 'pay' ) ) ) )
            );

        }

        /**
         * Output for the order received page.
         *
         * @access public
         * @return void
         */
        function receipt_page( $order ) {

            echo '<p>' . __( 'Thank you for your order, please click the button below to pay with PagSeguro.', 'wcpagseguro' ).'</p>';

            echo $this->generate_pagseguro_form( $order );

            // Update order status.
            $order = new WC_Order( $order );
            $order->update_status( 'on-hold', __( 'Awaiting payment via PagSeguro.', 'wcpagseguro' ) );
        }

    } // class WC_PagSeguro_Gateway
} // function wcpagseguro_gateway_load

/**
 * Hidden when the purchase is outside the Brazil.
 */
add_filter( 'woocommerce_available_payment_gateways', 'wcpagseguro_hidden_when_is_outside_brasil' );

function wcpagseguro_hidden_when_is_outside_brasil( $available_gateways ) {

    if ( isset( $_REQUEST['country'] ) && $_REQUEST['country'] != 'BR' ) {

        // remove standard shipping option
        unset( $available_gateways['pagseguro'] );
    }

    return $available_gateways;
}

/**
 * Process billing fields in checkout.
 */
add_action( 'woocommerce_checkout_process', 'wcpagseguro_checkout_valid_fields' );

function wcpagseguro_checkout_valid_fields() {
    global $woocommerce;

    // Valid address format.
    if ( $_POST['billing_address_1'] ) {

        $address = $_POST['billing_address_1'];
        $address = str_replace( ' ', '', $address );
        $pattern = '/([^\,\d]*),([0-9]*)/';
        $results = preg_match_all($pattern, $address, $out);

        if ( empty( $out[2] ) || !is_numeric( $out[2][0] ) ) {
            $woocommerce->add_error( __( '<strong>Address</strong> format is invalid. Example of correct format: "Av. Paulista, 460"', 'wcpagseguro' ) );
        }

    }
}
