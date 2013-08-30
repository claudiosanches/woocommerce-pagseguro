<?php
/**
 * WC PagSeguro Gateway Class.
 *
 * Built the PagSeguro method.
 *
 * @since 2.1.0
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
        $this->icon           = apply_filters( 'woocommerce_pagseguro_icon', WOO_PAGSEGURO_URL . 'images/pagseguro.png' );
        $this->has_fields     = false;
        $this->method_title   = __( 'PagSeguro', 'wcpagseguro' );

        // API URLs.
        $this->checkout_url   = 'https://ws.pagseguro.uol.com.br/v2/checkout';
        $this->payment_url    = 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=';
        $this->notify_url     = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/';

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
            echo '<div class="inline error"><p><strong>' . __( 'PagSeguro Disabled', 'wcpagseguro' ) . '</strong>: ' . __( 'Works only with Brazilian Real.', 'wcpagseguro' ) . '</p></div>';
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
                'desc_tip' => true,
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
                'description' => __( 'Please enter your PagSeguro email address. This is needed in order to take payment.', 'wcpagseguro' ),
                'desc_tip' => true,
                'default' => ''
            ),
            'token' => array(
                'title' => __( 'PagSeguro Token', 'wcpagseguro' ),
                'type' => 'text',
                'description' => sprintf( __( 'Please enter your PagSeguro token. This is needed to process the payment and notifications. Is possible generate a new token %s.', 'wcpagseguro' ), '<a href="https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml">' . __( 'here', 'wcpagseguro' ) . '</a>' ),
                'default' => ''
            ),
            'invoice_prefix' => array(
                'title' => __( 'Invoice Prefix', 'wcpagseguro' ),
                'type' => 'text',
                'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PagSeguro account for multiple stores ensure this prefix is unqiue as PagSeguro will not allow orders with the same invoice number.', 'wcpagseguro' ),
                'desc_tip' => true,
                'default' => 'WC-'
            ),
            'testing' => array(
                'title' => __( 'Gateway Testing', 'wcpagseguro' ),
                'type' => 'title',
                'description' => ''
            ),
            'debug' => array(
                'title' => __( 'Debug Log', 'wcpagseguro' ),
                'type' => 'checkbox',
                'label' => __( 'Enable logging', 'wcpagseguro' ),
                'default' => 'no',
                'description' => sprintf( __( 'Log PagSeguro events, such as API requests, inside %s', 'wcpagseguro' ), '<code>woocommerce/logs/pagseguro-' . sanitize_file_name( wp_hash( 'pagseguro' ) ) . '.txt</code>' )
            )
        );
    }

    /**
     * Add error message in checkout.
     *
     * @param string $message Error message.
     *
     * @return string         Displays the error message.
     */
    protected function add_error( $message ) {
        global $woocommerce;

        if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) )
            wc_add_error( $message );
        else
            $woocommerce->add_error( $message );
    }

    /**
     * Send email notification.
     *
     * @param  string $subject Email subject.
     * @param  string $title   Email title.
     * @param  string $message Email message.
     *
     * @return void
     */
    protected function send_email( $subject, $title, $message ) {
        global $woocommerce;

        $mailer = $woocommerce->mailer();

        $mailer->send( get_option( 'admin_email' ), $subject, $mailer->wrap_message( $title, $message ) );
    }

    /**
     * Generate the payment xml.
     *
     * @param object  $order Order data.
     *
     * @return string        Payment xml.
     */
    protected function generate_payment_xml( $order ) {
        // Include the WC_PagSeguro_SimpleXML class.
        require_once WOO_PAGSEGURO_PATH . 'includes/class-wc-pagseguro-simplexml.php';

        // Creates the payment xml.
        $xml = new WC_PagSeguro_SimpleXML( '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><checkout></checkout>' );

        // Currency.
        $xml->addChild( 'currency', get_woocommerce_currency() );

        // Reference.
        $xml->addChild( 'reference' )->addCData( $this->invoice_prefix . $order->id );

        // Receiver data.
        // $receiver = $xml->addChild( 'receiver' );
        // $receiver->addChild( 'email', $this->email );

        // Sender info.
        $sender = $xml->addChild( 'sender' );
        $sender->addChild( 'name' )->addCData( $order->billing_first_name . ' ' . $order->billing_last_name );
        $sender->addChild( 'email' )->addCData( $order->billing_email );
        // $documents = $sender->addChild( 'documents' );
        // $document = $documents->addChild( 'document' );
        // $document->addChild( 'type', 'CPF' );
        // $document->addChild( 'value', '' );

        if ( isset( $order->billing_phone ) && ! empty( $order->billing_phone ) ) {
            // Fix phone number.
            $order->billing_phone = str_replace( array( '(', '-', ' ', ')' ), '', $order->billing_phone );

            $phone = $sender->addChild( 'phone' );
            $phone->addChild( 'areaCode', substr( $order->billing_phone, 0, 2 ) );
            $phone->addChild( 'number', substr( $order->billing_phone, 2 ) );
        }

        // Shipping info.
        if ( isset( $order->billing_postcode ) && ! empty( $order->billing_postcode ) ) {
            $shipping = $xml->addChild( 'shipping' );
            $shipping->addChild( 'type', 3 );

            // Address infor
            $address = $shipping->addChild( 'address' );
            $address->addChild( 'street' )->addCData( $order->billing_address_1 );
            // $address->addChild( 'number', '' );
            if ( ! empty( $order->billing_address_2 ) )
                $address->addChild( 'complement' )->addCData( $order->billing_address_2 );
            // $address->addChild( 'district' )->addCData( '' );
            $address->addChild( 'postalCode', str_replace( array( '-', ' ' ), '', $order->billing_postcode ) );
            $address->addChild( 'city' )->addCData( $order->billing_city );
            $address->addChild( 'state', $order->billing_state );
            $address->addChild( 'country', 'BRA' );
        }

        // Items.
        $items = $xml->addChild( 'items' );

        // If prices include tax or have order discounts, send the whole order as a single item.
        if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) || $order->get_order_discount() > 0 ) {

            // Discount.
            if ( $order->get_order_discount() > 0 )
                $xml->addChild( 'extraAmount', '-' . $order->get_order_discount() );

            // Don't pass items - PagSeguro borks tax due to prices including tax.
            // PagSeguro has no option for tax inclusive pricing sadly. Pass 1 item for the order items overall.
            $item_names = array();

            if ( sizeof( $order->get_items() ) > 0 ) {
                foreach ( $order->get_items() as $order_item ) {
                    if ( $order_item['qty'] )
                        $item_names[] = $order_item['name'] . ' x ' . $order_item['qty'];
                }
            }

            $item = $items->addChild( 'item' );
            $item->addChild( 'id', 1 );
            $item->addChild( 'description' )->addCData( substr( sprintf( __( 'Order %s', 'wcpagseguro' ), $order->get_order_number() ) . ' - ' . implode( ', ', $item_names ), 0, 95 ) );
            $item->addChild( 'amount', number_format( $order->get_total() - $order->get_shipping() - $order->get_shipping_tax() + $order->get_order_discount(), 2, '.', '' ) );
            $item->addChild( 'quantity', 1 );

            if ( ( $order->get_shipping() + $order->get_shipping_tax() ) > 0 )
                $shipping->addChild( 'cost', number_format( $order->get_shipping() + $order->get_shipping_tax(), 2, '.', '' ) );

        } else {

            // Cart Contents.
            $item_loop = 0;
            if ( sizeof( $order->get_items() ) > 0 ) {
                foreach ( $order->get_items() as $order_item ) {
                    if ( $order_item['qty'] ) {
                        $item_loop++;
                        $product   = $order->get_product_from_item( $order_item );
                        $item_name = $order_item['name'];
                        $item_meta = new WC_Order_Item_Meta( $order_item['item_meta'] );

                        if ( $meta = $item_meta->display( true, true ) )
                            $item_name .= ' - ' . $meta;

                        $item = $items->addChild( 'item' );
                        $item->addChild( 'id', $item_loop );
                        $item->addChild( 'description' )->addCData( substr( sanitize_text_field( $item_name ), 0, 95 ) );
                        $item->addChild( 'amount', $order->get_item_total( $order_item, false ) );
                        $item->addChild( 'quantity', $order_item['qty'] );
                    }
                }
            }

            // Shipping Cost item.
            if ( $order->get_shipping() > 0 )
                $shipping->addChild( 'cost', number_format( $order->get_shipping(), 2, '.', '' ) );

            // Extras Amount.
            $xml->addChild( 'extraAmount', $order->get_total_tax() );
        }

        // Checks if is localhost. PagSeguro not accept localhost urls!
        if ( ! in_array( $_SERVER['HTTP_HOST'], array( 'localhost', '127.0.0.1' ) ) ) {
            $xml->addChild( 'redirectURL' )->addCData( $this->get_return_url( $order ) );
            $xml->addChild( 'notificationURL' )->addCData( home_url( '/?wc-api=WC_PagSeguro_Gateway' ) );
        }

        // Max uses.
        $xml->addChild( 'maxUses', 1 );

        // Max age.
        $xml->addChild( 'maxAge', 120 );

        // Filter the XML.
        $xml = apply_filters( 'woocommerce_pagseguro_payment_xml', $xml, $order );

        return $xml->asXML();
    }

    /**
     * Generate Payment Token.
     *
     * @param object $order Order data.
     *
     * @return bool
     */
    public function generate_payment_token( $order ) {
        global $woocommerce;

        // Include the WC_PagSeguro_Helpers class.
        require_once WOO_PAGSEGURO_PATH . 'includes/class-wc-pagseguro-helpers.php';
        $helper = new WC_PagSeguro_Helpers;

        // Sets the url.
        $url = esc_url_raw( sprintf(
            "%s?email=%s&token=%s",
            $this->checkout_url,
            $this->email,
            $this->token
        ) );

        // Sets the xml.
        $xml = $this->generate_payment_xml( $order );

        if ( 'yes' == $this->debug )
            $this->log->add( 'pagseguro', 'Requesting token for order ' . $order->get_order_number() . ' with the following data: ' . $xml );

        // Sets the post params.
        $params = array(
            'body'          => $xml,
            'sslverify'     => false,
            'timeout'       => 60,
            'headers'    => array(
                'Content-Type' => 'application/xml;charset=UTF-8',
            )
        );

        $response = wp_remote_post( $url, $params );

        if ( is_wp_error( $response ) ) {
            if ( 'yes' == $this->debug )
                $this->log->add( 'pagseguro', 'WP_Error in generate payment token: ' . $response->get_error_message() );
        } else {
            try {
                $body = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
            } catch ( Exception $e ) {
                $body = '';

                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'Error while parsing the PagSeguro response: ' . print_r( $e->getMessage(), true ) );
            }

            if ( isset( $body->code ) ) {
                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'PagSeguro Payment Token created with success! The Token is: ' . $body->code );

                return (string) $body->code;
            }

            if ( isset( $body->error ) ) {
                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'Failed to generate the PagSeguro Payment Token: ' . print_r( $response, true ) );

                foreach ( $body->error as $key => $value )
                    $this->add_error( '<strong>PagSeguro</strong>: ' . $helper->error_message( $value->code ) );

                return false;
            }

        }

        // Added error message.
        $this->add_error( '<strong>PagSeguro</strong>: ' . __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'wcpagseguro' ) );

        return false;
    }

    /**
     * Process the payment and return the result.
     *
     * @param int    $order_id Order ID.
     *
     * @return array           Redirect.
     */
    public function process_payment( $order_id ) {
        global $woocommerce;

        $order = new WC_Order( $order_id );

        $token = $this->generate_payment_token( $order );

        if ( $token ) {
            // Remove cart.
            $woocommerce->cart->empty_cart();

            return array(
                'result'   => 'success',
                'redirect' => esc_url_raw( $this->payment_url . $token )
            );
        }
    }

    /**
     * Process the IPN.
     *
     * @return bool
     */
    public function process_ipn_request( $data ) {

        if ( 'yes' == $this->debug )
            $this->log->add( 'pagseguro', 'Checking IPN request...' );

        // Valid the post data.
        if ( ! isset( $data['notificationCode'] ) && ! isset( $data['notificationType'] ) ) {
            if ( 'yes' == $this->debug )
                $this->log->add( 'pagseguro', 'Invalid IPN request: ' . print_r( $data, true ) );

            return false;
        }

        // Checks the notificationType.
        if ( 'transaction' != $data['notificationType'] ) {
            if ( 'yes' == $this->debug )
                $this->log->add( 'pagseguro', 'Invalid IPN request, invalid "notificationType": ' . print_r( $data, true ) );

            return false;
        }

        // Notification url.
        $url = esc_url_raw( sprintf(
            '%s%s?email=%s&token=%s',
            $this->notify_url,
            esc_attr( $data['notificationCode'] ),
            $this->email,
            $this->token
        ) );

        // Gets the PagSeguro response.
        $response = wp_remote_get( $url, array( 'timeout' => 60 ) );

        // Check to see if the request was valid.
        if ( is_wp_error( $response ) ) {
            if ( 'yes' == $this->debug )
                $this->log->add( 'pagseguro', 'WP_Error in IPN: ' . $response->get_error_message() );
        } else {
            try {
                $body = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
            } catch ( Exception $e ) {
                $body = '';

                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'Error while parsing the PagSeguro IPN response: ' . print_r( $e->getMessage(), true ) );
            }

            if ( isset( $body->code ) ) {
                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'PagSeguro IPN is valid! The return is: ' . print_r( $body, true ) );

                return $body;
            }
        }

        if ( 'yes' == $this->debug )
            $this->log->add( 'pagseguro', 'IPN Response: ' . print_r( $response, true ) );

        return false;
    }

    /**
     * Check API Response.
     *
     * @return void
     */
    public function check_ipn_response() {
        @ob_clean();

        $ipn = $this->process_ipn_request( $_POST );

        if ( $ipn ) {
            header( 'HTTP/1.1 200 OK' );
            do_action( 'valid_pagseguro_ipn_request', $ipn );
        } else {
            wp_die( __( 'PagSeguro Request Failure', 'wcpagseguro' ) );
        }
    }

    /**
     * Successful Payment!
     *
     * @param array $posted PagSeguro post data.
     *
     * @return void
     */
    public function successful_request( $posted ) {

        if ( isset( $posted->reference ) ) {
            $order_id = (int) str_replace( $this->invoice_prefix, '', $posted->reference );

            $order = new WC_Order( $order_id );

            // Checks whether the invoice number matches the order.
            // If true processes the payment.
            if ( $order->id === $order_id ) {
                // Include the WC_PagSeguro_Helpers class.
                require_once WOO_PAGSEGURO_PATH . 'includes/class-wc-pagseguro-helpers.php';
                $helper = new WC_PagSeguro_Helpers;

                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'PagSeguro payment status for order ' . $order->get_order_number() . ' is: ' . $posted->status );

                switch ( (int) $posted->status ) {
                    case 1:
                        $order->add_order_note( __( 'PagSeguro: Awaiting payment.', 'wcpagseguro' ) );

                        break;
                    case 2:
                        $order->update_status( 'on-hold', __( 'PagSeguro: Payment under review.', 'wcpagseguro' ) );

                        break;
                    case 3:
                        // Order details.
                        if ( isset( $posted->code ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'PagSeguro Transaction ID', 'wcpagseguro' ),
                                (string) $posted->code
                            );
                        }
                        if ( isset( $posted->sender->email ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payer email', 'wcpagseguro' ),
                                (string) $posted->sender->email
                            );
                        }
                        if ( isset( $posted->sender->name ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payer name', 'wcpagseguro' ),
                                (string) $posted->sender->name
                            );
                        }
                        if ( isset( $posted->paymentMethod->type ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payment type', 'wcpagseguro' ),
                                $helper->payment_type( (int) $posted->paymentMethod->type )
                            );
                        }
                        if ( isset( $posted->paymentMethod->code ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payment method', 'wcpagseguro' ),
                                $helper->payment_method( (int) $posted->paymentMethod->code )
                            );
                        }
                        if ( isset( $posted->installmentCount ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Installments', 'wcpagseguro' ),
                                (string) $posted->installmentCount
                            );
                        }
                        if ( isset( $posted->paymentLink ) ) {
                            update_post_meta(
                                $order_id,
                                __( 'Payment url', 'wcpagseguro' ),
                                (string) $posted->paymentLink
                            );
                        }

                        $order->add_order_note( __( 'PagSeguro: Payment approved.', 'wcpagseguro' ) );

                        // Changing the order for processing and reduces the stock.
                        $order->payment_complete();

                        break;
                    case 4:
                        $order->add_order_note( __( 'PagSeguro: Payment completed and credited to your account.', 'wcpagseguro' ) );

                        break;
                    case 5:
                        $order->update_status( 'on-hold', __( 'PagSeguro: Payment came into dispute.', 'wcpagseguro' ) );
                        $this->send_email(
                            sprintf( __( 'Payment for order %s came into dispute', 'wcpagseguro' ), $order->get_order_number() ),
                            __( 'Payment in dispute', 'wcpagseguro' ),
                            sprintf( __( 'Order %s has been marked as on-hold, because the payment came into dispute in PagSeguro.', 'wcpagseguro' ), $order->get_order_number() )
                        );

                        break;
                    case 6:
                        $order->update_status( 'refunded', __( 'PagSeguro: Payment refunded.', 'wcpagseguro' ) );
                        $this->send_email(
                            sprintf( __( 'Payment for order %s refunded', 'wcpagseguro' ), $order->get_order_number() ),
                            __( 'Payment refunded', 'wcpagseguro' ),
                            sprintf( __( 'Order %s has been marked as refunded by PagSeguro.', 'wcpagseguro' ), $order->get_order_number() )
                        );

                        break;
                    case 7:
                        $order->update_status( 'cancelled', __( 'PagSeguro: Payment canceled.', 'wcpagseguro' ) );

                        break;

                    default:
                        // No action xD.
                        break;
                }
            } else {
                if ( 'yes' == $this->debug )
                    $this->log->add( 'pagseguro', 'Error: Order Key does not match with PagSeguro reference.' );
            }
        }
    }

    /**
     * Adds error message when not configured the email.
     *
     * @return string Error Mensage.
     */
    public function mail_missing_message() {
        echo '<div class="error"><p><strong>' . __( 'PagSeguro Disabled', 'wcpagseguro' ) . '</strong>: ' . sprintf( __( 'You should inform your email address. %s', 'wcpagseguro' ), '<a href="' . admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_PagSeguro_Gateway' ) . '">' . __( 'Click here to configure!', 'wcpagseguro' ) . '</a>' ) . '</p></div>';
    }

    /**
     * Adds error message when not configured the token.
     *
     * @return string Error Mensage.
     */
    public function token_missing_message() {
        echo '<div class="error"><p><strong>' . __( 'PagSeguro Disabled', 'wcpagseguro' ) . '</strong>: ' . sprintf( __( 'You should inform your token. %s', 'wcpagseguro' ), '<a href="' . admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_PagSeguro_Gateway' ) . '">' . __( 'Click here to configure!', 'wcpagseguro' ) . '</a>' ) . '</p></div>';
    }

}
