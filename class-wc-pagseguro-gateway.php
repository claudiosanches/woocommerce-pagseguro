<?php
/**
 * WC PagSeguro Gateway Class.
 *
 * Built the PagSeguro method.
 */
class WC_PagSeguro_Gateway extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     *
     * @return void
     */
    public function __construct() {
        global $woocommerce;

        $this->id             = 'pagseguro';
        $this->icon           = plugins_url( 'images/pagseguro.png', __FILE__ );
        $this->has_fields     = false;
        $this->payment_url    = 'https://pagseguro.uol.com.br/v2/checkout/payment.html';
        $this->ipn_url        = 'https://pagseguro.uol.com.br/pagseguro-ws/checkout/NPI.jhtml';
        $this->method_title   = __( 'PagSeguro', 'wcpagseguro' );

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables.
        $this->title          = $this->settings['title'];
        $this->description    = $this->settings['description'];
        $this->email          = $this->settings['email'];
        $this->token          = $this->settings['token'];
        $this->invoice_prefix = ! empty( $this->settings['invoice_prefix'] ) ? $this->settings['invoice_prefix'] : 'WC-';
        $this->debug          = $this->settings['debug'];

        // Actions.
        add_action( 'woocommerce_api_wc_pagseguro_gateway', array( &$this, 'check_ipn_response' ) );
        add_action( 'valid_pagseguro_ipn_request', array( &$this, 'successful_request' ) );
        add_action( 'woocommerce_receipt_pagseguro', array( &$this, 'receipt_page' ) );
        if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) )
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
        else
            add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );

        // Valid for use.
        $this->enabled = ( 'yes' == $this->settings['enabled'] ) && ! empty( $this->email ) && ! empty( $this->token ) && $this->is_valid_for_use();

        // Checks if email is not empty.
        if ( empty( $this->email ) )
            add_action( 'admin_notices', array( &$this, 'mail_missing_message' ) );

        // Checks if token is not empty.
        if ( empty( $this->token ) )
            add_action( 'admin_notices', array( &$this, 'token_missing_message' ) );

        // Active logs.
        if ( 'yes' == $this->debug )
            $this->log = $woocommerce->logger();
    }

    /**
     * Check if this gateway is enabled and available in the user's country.
     *
     * @return bool
     */
    public function is_valid_for_use() {
        if ( ! in_array( get_woocommerce_currency(), array( 'BRL' ) ) )
            return false;

        return true;
    }

    /**
     * Admin Panel Options.
     */
    public function admin_options() {
        echo '<h3>' . __( 'PagSeguro standard', 'wcpagseguro' ) . '</h3>';
        echo '<p>' . __( 'PagSeguro standard works by sending the user to PagSeguro to enter their payment information.', 'wcpagseguro' ) . '</p>';

        // Checks if is valid for use.
        if ( ! $this->is_valid_for_use() ) {
            echo '<div class="inline error"><p><strong>' . __( 'Gateway Disabled', 'wcpagseguro' ) . '</strong>: ' . __( 'PagSeguro does not support your store currency.', 'wcpagseguro' ) . '</p></div>';

        } else {
            // Generate the HTML For the settings form.
            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
        }
    }

    /**
     * Initialise Gateway Settings Form Fields.
     *
     * @return void
     */
    public function init_form_fields() {

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
            'token' => array(
                'title' => __( 'PagSeguro Token', 'wcpagseguro' ),
                'type' => 'text',
                'description' => sprintf( __( 'Please enter your PagSeguro token; is necessary to process the payment and notifications. Is possible generate a new token %shere%s', 'wcpagseguro' ), '<a href="https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml">', '</a>' ),
                'default' => ''
            ),
            'invoice_prefix' => array(
                'title' => __( 'Invoice Prefix', 'wcpagseguro' ),
                'type' => 'text',
                'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PagSeguro account for multiple stores ensure this prefix is unqiue as PagSeguro will not allow orders with the same invoice number.', 'wcpagseguro' ),
                'default' => 'WC-'
            ),
            'testing' => array(
                'title' => __( 'Gateway Testing', 'wcpagseguro' ),
                'type' => 'title',
                'description' => '',
            ),
            'debug' => array(
                'title' => __( 'Debug Log', 'wcpagseguro' ),
                'type' => 'checkbox',
                'label' => __( 'Enable logging', 'wcpagseguro' ),
                'default' => 'no',
                'description' => __( 'Log PagSeguro events, such as API requests, inside <code>woocommerce/logs/pagseguro.txt</code>', 'wcpagseguro' ),
            )
        );
    }

    /**
     * Generate the args to form.
     *
     * @param  array $order Order data.
     *
     * @return array
     */
    public function get_form_args( $order ) {

        // Fix phone number.
        $order->billing_phone = str_replace( array( '(', '-', ' ', ')' ), '', $order->billing_phone );

        // Fix postal code.
        $order->billing_postcode = str_replace( array( '-', ' ' ), '', $order->billing_postcode );

        $args = array(
            'receiverEmail'             => $this->email,
            'currency'                  => get_woocommerce_currency(),
            'encoding'                  => 'UTF-8',

            // Sender info.
            'senderName'                => $order->billing_first_name . ' ' . $order->billing_last_name,
            'senderEmail'               => $order->billing_email,
            'senderAreaCode'            => substr( $order->billing_phone, 0, 2 ),
            'senderPhone'               => substr( $order->billing_phone, 2 ),

            // Address info.
            'shippingAddressPostalCode' => $order->billing_postcode,
            'shippingAddressStreet'     => $order->billing_address_1,
            'shippingAddressComplement' => $order->billing_address_2,
            'shippingAddressCity'       => $order->billing_city,
            'shippingAddressState'      => $order->billing_state,
            'shippingAddressCountry'    => 'BRA',

            // Extras.
            'extraAmount'               => $order->get_total_tax(),

            // Payment Info.
            'reference'                 => $this->invoice_prefix . $order->id
        );

        // If prices include tax or have order discounts, send the whole order as a single item.
        if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) || $order->get_order_discount() > 0 ) {

            // Discount.
            if ( $order->get_order_discount() > 0 )
                $args['extraAmount'] = '-' . $order->get_order_discount();
            else
                $args['extraAmount'] = '';

            // Don't pass items - pagseguro borks tax due to prices including tax.
            // PagSeguro has no option for tax inclusive pricing sadly. Pass 1 item for the order items overall.
            $item_names = array();

            if ( sizeof( $order->get_items() ) > 0 ) {
                foreach ( $order->get_items() as $item ) {
                    if ( $item['qty'] )
                        $item_names[] = $item['name'] . ' x ' . $item['qty'];
                }
            }

            $args['itemId1']          = 1;
            $args['itemDescription1'] = substr( sprintf( __( 'Order %s', 'wcpagseguro' ), $order->get_order_number() ) . ' - ' . implode( ', ', $item_names ), 0, 95 );
            $args['itemQuantity1']    = 1;
            $args['itemAmount1']      = number_format( $order->get_total() - $order->get_shipping() - $order->get_shipping_tax() + $order->get_order_discount(), 2, '.', '' );

            if ( ( $order->get_shipping() + $order->get_shipping_tax() ) > 0 ) {
                $args['itemId2']          = 2;
                $args['itemDescription2'] = __( 'Shipping via', 'wcpagseguro' ) . ' ' . ucwords( $order->shipping_method_title );
                $args['itemQuantity2']    = '1';
                $args['itemAmount2']      = number_format( $order->get_shipping() + $order->get_shipping_tax(), 2, '.', '' );
            }

        } else {

            // Cart Contents.
            $item_loop = 0;
            if ( sizeof( $order->get_items() ) > 0 ) {
                foreach ( $order->get_items() as $item ) {
                    if ( $item['qty'] ) {
                        $item_loop++;
                        $product   = $order->get_product_from_item( $item );
                        $item_name = $item['name'];
                        $item_meta = new WC_Order_Item_Meta( $item['item_meta'] );

                        if ( $meta = $item_meta->display( true, true ) )
                            $item_name .= ' - ' . $meta;

                        $args['itemId' . $item_loop]          = $item_loop;
                        $args['itemDescription' . $item_loop] = substr( sanitize_text_field( $item_name ), 0, 95 );
                        $args['itemQuantity' . $item_loop]    = $item['qty'];
                        $args['itemAmount' . $item_loop]      = $order->get_item_total( $item, false );
                    }
                }
            }

            // Shipping Cost item.
            if ( $order->get_shipping() > 0 ) {
                $item_loop++;
                $args['itemId' . $item_loop]          = $item_loop;
                $args['itemDescription' . $item_loop] = substr( __( 'Shipping via', 'wcpagseguro' ) . ' ' . ucwords( $order->shipping_method_title ), 0, 100 );
                $args['itemQuantity' . $item_loop]    = '1';
                $args['itemAmount' . $item_loop]      = number_format( $order->get_shipping(), 2, '.', '' );
            }
        }

        $args = apply_filters( 'woocommerce_pagseguro_args', $args, $order->id );

        return $args;
    }

    /**
     * Generate the form.
     *
     * @param mixed $order_id
     *
     * @return string
     */
    public function generate_form( $order_id ) {
        global $woocommerce;

        $order = new WC_Order( $order_id );
        $args  = $this->get_form_args( $order );

        if ( 'yes' == $this->debug )
            $this->log->add( 'pagseguro', 'Payment arguments for order #' . $order_id . ': ' . print_r( $args, true ) );

        $args_array = array();

        foreach ( $args as $key => $value )
            $args_array[] = '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';

        if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
            $woocommerce->get_helper( 'inline-javascript' )->add_inline_js( '
                $.blockUI({
                        message: "' . esc_js( __( 'Thank you for your order. We are now redirecting you to PagSeguro to make payment.', 'wcpagseguro' ) ) . '",
                        baseZ: 99999,
                        overlayCSS:
                        {
                            background: "#fff",
                            opacity: 0.6
                        },
                        css: {
                            padding:        "20px",
                            zIndex:         "9999999",
                            textAlign:      "center",
                            color:          "#555",
                            border:         "3px solid #aaa",
                            backgroundColor:"#fff",
                            cursor:         "wait",
                            lineHeight:     "24px",
                        }
                    });
                jQuery("#submit-payment-form").click();
            ' );
        } else {
            $woocommerce->add_inline_js( '
                jQuery("body").block({
                        message: "<img src=\"' . esc_url( $woocommerce->plugin_url() . '/assets/images/ajax-loader.gif' ) . '\" alt=\"Redirecting&hellip;\" style=\"float:left; margin-right: 10px;\" />' . __( 'Thank you for your order. We are now redirecting you to PagSeguro to make payment.', 'wcpagseguro' ) . '",
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
                jQuery("#submit-payment-form").click();
            ' );
        }

        return '<form action="' . esc_url( $this->payment_url ) . '" method="post" id="payment-form" target="_top">
                ' . implode( '', $args_array ) . '
                <input type="submit" class="button alt" id="submit-payment-form" value="' . __( 'Pay via PagSeguro', 'wcpagseguro' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'wcpagseguro' ) . '</a>
            </form>';

    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id
     *
     * @return array
     */
    public function process_payment( $order_id ) {
        $order = new WC_Order( $order_id );

        if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
            return array(
                'result'   => 'success',
                'redirect' => $order->get_checkout_payment_url( true )
            );
        } else {
            return array(
                'result'   => 'success',
                'redirect' => add_query_arg( 'order', $order->id, add_query_arg( 'key', $order->order_key, get_permalink( woocommerce_get_page_id( 'pay' ) ) ) )
            );
        }
    }

    /**
     * Output for the order received page.
     *
     * @return void
     */
    public function receipt_page( $order ) {
        global $woocommerce;

        echo '<p>' . __( 'Thank you for your order, please click the button below to pay with PagSeguro.', 'wcpagseguro' ) . '</p>';

        echo $this->generate_form( $order );

        // Remove cart.
        $woocommerce->cart->empty_cart();
    }

    /**
     * Check ipn validity.
     *
     * @return bool
     */
    public function check_ipn_request_is_valid() {

        if ( 'yes' == $this->debug )
            $this->log->add( 'pagseguro', 'Checking IPN request...' );

        $received_values = (array) stripslashes_deep( $_POST );
        $postdata = http_build_query( $received_values, '', '&' );
        $postdata .= '&Comando=validar&Token=' . $this->token;

        // Send back post vars.
        $params = array(
            'body'          => $postdata,
            'sslverify'     => false,
            'timeout'       => 30
        );

        // Post back to get a response.
        $response = wp_remote_post( $this->ipn_url, $params );

        if ( 'yes' == $this->debug )
            $this->log->add( 'pagseguro', 'IPN Response: ' . print_r( $response, true ) );

        // Check to see if the request was valid.
        if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && ( strcmp( $response['body'], 'VERIFICADO' ) == 0 ) ) {

            if ( 'yes' == $this->debug )
                $this->log->add( 'pagseguro', 'Received valid IPN response from PagSeguro' );

            return true;
        } else {
            if ( 'yes' == $this->debug )
                $this->log->add( 'pagseguro', 'Received invalid IPN response from PagSeguro.' );
        }

        return false;
    }

    /**
     * Check API Response.
     *
     * @return void
     */
    public function check_ipn_response() {

        @ob_clean();

        if ( ! empty( $_POST ) && ! empty( $this->token ) && $this->check_ipn_request_is_valid() ) {

            header( 'HTTP/1.1 200 OK' );

            do_action( 'valid_pagseguro_ipn_request', $_POST );

        } else {

            wp_die( __( 'PagSeguro Request Failure', 'wcpagseguro' ) );

        }
    }

    /**
     * Successful Payment!
     *
     * @param array $posted
     *
     * @return void
     */
    public function successful_request( $received_values ) {

        $posted = (array) stripslashes_deep( $received_values );

        if ( ! empty( $posted['Referencia'] ) ) {
            $order_key = $posted['Referencia'];
            $order_id = (int) str_replace( $this->invoice_prefix, '', $order_key );

            $order = new WC_Order( $order_id );

            // Checks whether the invoice number matches the order.
            // If true processes the payment.
            if ( $order->id === $order_id ) {

                $order_status = sanitize_title( $posted['StatusTransacao'] );

                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'Payment status from order #' . $order->id . ': ' . $posted['StatusTransacao'] );

                switch ( $order_status ) {
                    case 'completo':

                        // Order details.
                        if ( ! empty( $posted['TransacaoID'] ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'PagSeguro Transaction ID', 'wcpagseguro' ),
                                $posted['TransacaoID']
                            );
                        }
                        if ( ! empty( $posted['CliEmail'] ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payer email', 'wcpagseguro' ),
                                $posted['CliEmail']
                            );
                        }
                        if ( ! empty( $posted['CliNome'] ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payer name', 'wcpagseguro' ),
                                $posted['CliNome']
                            );
                        }
                        if ( ! empty( $posted['TipoPagamento'] ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payment type', 'wcpagseguro' ),
                                $posted['TipoPagamento']
                            );
                        }

                        $order->add_order_note( __( 'Payment completed.', 'wcpagseguro' ) );

                        break;
                    case 'aguardando-pagto':
                        $order->add_order_note( __( 'Awaiting payment.', 'wcpagseguro' ) );

                        break;
                    case 'aprovado':
                        $order->add_order_note( __( 'Payment approved, awaiting compensation.', 'wcpagseguro' ) );

                        // Changing the order for processing and reduces the stock.
                        $order->payment_complete();

                        break;
                    case 'em-analise':
                        $order->update_status( 'on-hold', __( 'Payment approved, under review by PagSeguro.', 'wcpagseguro' ) );

                        break;
                    case 'cancelado':
                        $order->update_status( 'cancelled', __( 'Payment canceled by PagSeguro.', 'wcpagseguro' ) );

                        break;

                    default:
                        // No action xD.
                        break;
                }
            }
        }
    }

    /**
     * Adds error message when not configured the email.
     *
     * @return string Error Mensage.
     */
    public function mail_missing_message() {
        $html = '<div class="error">';
            $html .= '<p>' . sprintf( __( '<strong>Gateway Disabled</strong> You should inform your email address in PagSeguro. %sClick here to configure!%s', 'wcpagseguro' ), '<a href="' . get_admin_url() . 'admin.php?page=woocommerce_settings&amp;tab=payment_gateways">', '</a>' ) . '</p>';
        $html .= '</div>';

        echo $html;
    }

    /**
     * Adds error message when not configured the token.
     *
     * @return string Error Mensage.
     */
    public function token_missing_message() {
        $html = '<div class="error">';
            $html .= '<p>' . sprintf( __( '<strong>Gateway Disabled</strong> You should inform your token in PagSeguro. %sClick here to configure!%s', 'wcpagseguro' ), '<a href="' . get_admin_url() . 'admin.php?page=woocommerce_settings&amp;tab=payment_gateways">', '</a>' ) . '</p>';
        $html .= '</div>';

        echo $html;
    }

} // close WC_PagSeguro_Gateway class.
