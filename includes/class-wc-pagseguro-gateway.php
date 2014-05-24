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
		$this->id                = WC_PagSeguro::get_gateway_id();
		$this->icon              = apply_filters( 'woocommerce_pagseguro_icon', plugins_url( 'images/pagseguro.png', plugin_dir_path( __FILE__ ) ) );
		$this->has_fields        = false;
		$this->method_title      = __( 'PagSeguro', 'woocommerce-pagseguro' );
		$this->order_button_text = __( 'Proceed to payment', 'woocommerce-pagseguro' );

		// API URLs.
		$this->checkout_url = 'https://ws.pagseguro.uol.com.br/v2/checkout';
		$this->payment_url  = 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=';
		$this->notify_url   = 'https://ws.pagseguro.uol.com.br/v2/transactions/notifications/';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		$this->email          = $this->get_option( 'email' );
		$this->token          = $this->get_option( 'token' );
		$this->method         = $this->get_option( 'method', 'direct' );
		$this->invoice_prefix = $this->get_option( 'invoice_prefix', 'WC-' );
		$this->debug          = $this->get_option( 'debug' );

		// Actions.
		add_action( 'woocommerce_api_wc_pagseguro_gateway', array( $this, 'check_ipn_response' ) );
		add_action( 'valid_pagseguro_ipn_request', array( $this, 'successful_request' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		// Active logs.
		if ( 'yes' == $this->debug ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->log = new WC_Logger();
			} else {
				$this->log = $this->woocommerce_instance()->logger();
			}
		}

		// Display admin notices.
		$this->admin_notices();
	}

	/**
	 * Backwards compatibility with version prior to 2.1.
	 *
	 * @return object Returns the main instance of WooCommerce class.
	 */
	protected function woocommerce_instance() {
		if ( function_exists( 'WC' ) ) {
			return WC();
		} else {
			global $woocommerce;
			return $woocommerce;
		}
	}

	/**
	 * Displays notifications when the admin has something wrong with the configuration.
	 *
	 * @return void
	 */
	protected function admin_notices() {
		if ( is_admin() ) {
			// Checks if email is not empty.
			if ( empty( $this->email ) ) {
				add_action( 'admin_notices', array( $this, 'mail_missing_message' ) );
			}

			// Checks if token is not empty.
			if ( empty( $this->token ) ) {
				add_action( 'admin_notices', array( $this, 'token_missing_message' ) );
			}

			// Checks that the currency is supported
			if ( ! $this->using_supported_currency() ) {
				add_action( 'admin_notices', array( $this, 'currency_not_supported_message' ) );
			}
		}
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency() {
		return in_array( get_woocommerce_currency(), array( 'BRL' ) );
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available() {
		// Test if is valid for use.
		$available = ( 'yes' == $this->settings['enabled'] ) &&
					! empty( $this->email ) &&
					! empty( $this->token ) &&
					$this->using_supported_currency();

		return $available;
	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options() {
		echo '<h3>' . __( 'PagSeguro standard', 'woocommerce-pagseguro' ) . '</h3>';
		echo '<p>' . __( 'PagSeguro standard works by sending the user to PagSeguro to enter their payment information.', 'woocommerce-pagseguro' ) . '</p>';

		// Generate the HTML For the settings form.
		echo '<table class="form-table">';
		$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'woocommerce-pagseguro' ),
				'type' => 'checkbox',
				'label' => __( 'Enable PagSeguro standard', 'woocommerce-pagseguro' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title', 'woocommerce-pagseguro' ),
				'type' => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-pagseguro' ),
				'desc_tip' => true,
				'default' => __( 'PagSeguro', 'woocommerce-pagseguro' )
			),
			'description' => array(
				'title' => __( 'Description', 'woocommerce-pagseguro' ),
				'type' => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-pagseguro' ),
				'default' => __( 'Pay via PagSeguro', 'woocommerce-pagseguro' )
			),
			'email' => array(
				'title' => __( 'PagSeguro Email', 'woocommerce-pagseguro' ),
				'type' => 'text',
				'description' => __( 'Please enter your PagSeguro email address. This is needed in order to take payment.', 'woocommerce-pagseguro' ),
				'desc_tip' => true,
				'default' => ''
			),
			'token' => array(
				'title' => __( 'PagSeguro Token', 'woocommerce-pagseguro' ),
				'type' => 'text',
				'description' => sprintf( __( 'Please enter your PagSeguro token. This is needed to process the payment and notifications. Is possible generate a new token %s.', 'woocommerce-pagseguro' ), '<a href="https://pagseguro.uol.com.br/integracao/token-de-seguranca.jhtml">' . __( 'here', 'woocommerce-pagseguro' ) . '</a>' ),
				'default' => ''
			),
			'method' => array(
				'title' => __( 'Integration method', 'woocommerce-pagseguro' ),
				'type' => 'select',
				'description' => __( 'Choose how the customer will interact with the PagSeguro. Redirect (Client goes to PagSeguro page) or Lightbox (Inside your store)', 'woocommerce-pagseguro' ),
				'desc_tip' => true,
				'default' => 'direct',
				'options' => array(
					'redirect' => __( 'Redirect (default)', 'woocommerce-pagseguro' ),
					'lightbox' => __( 'Lightbox', 'woocommerce-pagseguro' )
				)
			),
			'invoice_prefix' => array(
				'title' => __( 'Invoice Prefix', 'woocommerce-pagseguro' ),
				'type' => 'text',
				'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PagSeguro account for multiple stores ensure this prefix is unqiue as PagSeguro will not allow orders with the same invoice number.', 'woocommerce-pagseguro' ),
				'desc_tip' => true,
				'default' => 'WC-'
			),
			'testing' => array(
				'title' => __( 'Gateway Testing', 'woocommerce-pagseguro' ),
				'type' => 'title',
				'description' => ''
			),
			'debug' => array(
				'title' => __( 'Debug Log', 'woocommerce-pagseguro' ),
				'type' => 'checkbox',
				'label' => __( 'Enable logging', 'woocommerce-pagseguro' ),
				'default' => 'no',
				'description' => sprintf( __( 'Log PagSeguro events, such as API requests, inside %s', 'woocommerce-pagseguro' ), '<code>woocommerce/logs/' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>' )
			)
		);
	}

	/**
	 * Add error messages in checkout.
	 *
	 * @param string $messages Error message.
	 *
	 * @return string          Displays the error messages.
	 */
	protected function add_error( $messages ) {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
			foreach ( $messages as $message ) {
				wc_add_notice( $message, 'error' );
			}
		} else {
			foreach ( $messages as $message ) {
				$this->woocommerce_instance()->add_error( $message );
			}
		}
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
		$mailer = $this->woocommerce_instance()->mailer();

		$mailer->send( get_option( 'admin_email' ), $subject, $mailer->wrap_message( $title, $message ) );
	}

	/**
	 * Fix money format.
	 * Adds support to WooCommerce 2.1 or later.
	 *
	 * @param  int/float $value Value to fix.
	 *
	 * @return float            Fixed value.
	 */
	protected function fix_money_format( $value ) {
		return number_format( $value, 2, '.', '' );
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
		require_once plugin_dir_path( __FILE__ ) . 'class-wc-pagseguro-simplexml.php';

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
			if ( ! empty( $order->billing_address_2 ) ) {
				$address->addChild( 'complement' )->addCData( $order->billing_address_2 );
			}
			// $address->addChild( 'district' )->addCData( '' );
			$address->addChild( 'postalCode', str_replace( array( '-', ' ' ), '', $order->billing_postcode ) );
			$address->addChild( 'city' )->addCData( $order->billing_city );
			$address->addChild( 'state', $order->billing_state );
			$address->addChild( 'country', 'BRA' );
		}

		// Items.
		$items = $xml->addChild( 'items' );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
			$shipping_total = $this->fix_money_format( $order->get_total_shipping() );
		} else {
			$shipping_total = $this->fix_money_format( $order->get_shipping() );
		}

		// If prices include tax or have order discounts, send the whole order as a single item.
		if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) || $order->get_order_discount() > 0 ) {

			// Discount.
			if ( $order->get_order_discount() > 0 ) {
				$xml->addChild( 'extraAmount', '-' . $this->fix_money_format( $order->get_order_discount() ) );
			}

			// Don't pass items - PagSeguro borks tax due to prices including tax.
			// PagSeguro has no option for tax inclusive pricing sadly. Pass 1 item for the order items overall.
			$item_names = array();

			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $order_item ) {
					if ( $order_item['qty'] ) {
						$item_names[] = $order_item['name'] . ' x ' . $order_item['qty'];
					}
				}
			}

			$item = $items->addChild( 'item' );
			$item->addChild( 'id', 1 );
			$item->addChild( 'description' )->addCData( substr( sprintf( __( 'Order %s', 'woocommerce-pagseguro' ), $order->get_order_number() ) . ' - ' . implode( ', ', $item_names ), 0, 95 ) );
			$item->addChild( 'amount', $this->fix_money_format( $order->get_total() - $shipping_total - $order->get_shipping_tax() + $order->get_order_discount() ) );
			$item->addChild( 'quantity', 1 );

			if ( ( $shipping_total + $order->get_shipping_tax() ) > 0 ) {
				$shipping->addChild( 'cost', $this->fix_money_format( $shipping_total + $order->get_shipping_tax(), 2, '.', '' ) );
			}

		} else {

			// Cart Contents.
			$item_loop = 0;
			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $order_item ) {
					if ( $order_item['qty'] ) {
						$item_loop++;
						$item_name = $order_item['name'];
						$item_meta = new WC_Order_Item_Meta( $order_item['item_meta'] );

						if ( $meta = $item_meta->display( true, true ) ) {
							$item_name .= ' - ' . $meta;
						}

						$item = $items->addChild( 'item' );
						$item->addChild( 'id', $item_loop );
						$item->addChild( 'description' )->addCData( substr( sanitize_text_field( $item_name ), 0, 95 ) );
						$item->addChild( 'amount', $this->fix_money_format( $order->get_item_total( $order_item, false ) ) );
						$item->addChild( 'quantity', $order_item['qty'] );
					}
				}
			}

			// Shipping Cost item.
			if ( $shipping_total > 0 ) {
				$shipping->addChild( 'cost', $this->fix_money_format( $shipping_total, 2, '.', '' ) );
			}

			// Extras Amount.
			$xml->addChild( 'extraAmount', $this->fix_money_format( $order->get_total_tax() ) );
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
	 * @return array
	 */
	public function generate_payment_token( $order ) {
		// Include the WC_PagSeguro_Helpers class.
		require_once plugin_dir_path( __FILE__ ) . 'class-wc-pagseguro-helpers.php';
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

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Requesting token for order ' . $order->get_order_number() . ' with the following data: ' . $xml );
		}

		// Sets the post params.
		$params = array(
			'body'      => $xml,
			'sslverify' => false,
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type' => 'application/xml;charset=UTF-8',
			)
		);

		$response = wp_remote_post( $url, $params );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'WP_Error in generate payment token: ' . $response->get_error_message() );
			}
		} else {
			try {
				$body = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				$body = '';

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error while parsing the PagSeguro response: ' . print_r( $e->getMessage(), true ) );
				}
			}

			if ( isset( $body->code ) ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'PagSeguro Payment Token created with success! The Token is: ' . $body->code );
				}

				return array(
					'token' => (string) $body->code,
					'error' => ''
				);
			}

			if ( isset( $body->error ) ) {
				$errors = array();

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Failed to generate the PagSeguro Payment Token: ' . print_r( $response, true ) );
				}

				foreach ( $body->error as $key => $value ) {
					$errors[] = '<strong>PagSeguro</strong>: ' . $helper->error_message( $value->code );
				}

				return array(
					'token' => '',
					'error' => $errors
				);
			}

		}

		// return error message.
		return array(
			'token' => '',
			'error' => array( '<strong>PagSeguro</strong>: ' . __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'woocommerce-pagseguro' ) )
		);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int    $order_id Order ID.
	 *
	 * @return array           Redirect.
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( 'redirect' == $this->method ) {
			$token = $this->generate_payment_token( $order );

			if ( $token['token'] ) {
				// Remove cart.
				$this->woocommerce_instance()->cart->empty_cart();

				return array(
					'result'   => 'success',
					'redirect' => esc_url_raw( $this->payment_url . $token['token'] )
				);
			} else {
				$this->add_error( $token['error'] );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			}
		} else {
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
	}

	/**
	 * Output for the order received page.
	 *
	 * @param  $order_id Order ID.
	 *
	 * @return string    PagSeguro lightbox.
	 */
	public function receipt_page( $order_id ) {
		$order = new WC_Order( $order_id );
		$token = $this->generate_payment_token( $order );

		if ( $token['token'] ) {

			// Display checkout.
			$html = '<p id="browser-has-javascript" style="display: none;">' . __( 'Thank you for your order, please wait a few seconds to make the payment with PagSeguro.', 'woocommerce-pagseguro' ) . '</p>';

			$html .= '<p id="browser-no-has-javascript">' . __( 'Thank you for your order, please click the button below to pay with PagSeguro.', 'woocommerce-pagseguro' ) . '</p>';

			$html .= '<a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woocommerce-pagseguro' ) . '</a> <a id="submit-payment" class="button alt" href="' . esc_url_raw( $this->payment_url . $token['token'] ) . '">' . __( 'Pay via PagSeguro', 'woocommerce-pagseguro' ) . '</a>';

			// PagSeguro lightbox API.
			$html .= '<script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>';

			// Payment script.
			$js = '
				document.getElementById( "submit-payment" ).style.display = "none";
				document.getElementById( "browser-has-javascript" ).style.display = "block";
				document.getElementById( "browser-no-has-javascript" ).style.display = "none";
				var code = "' . esc_attr( $token['token'] ) . '";
				var isOpenLightbox = PagSeguroLightbox({
						code: code
					}, {
						success: function( transactionCode ) {
							window.location.href="' . str_replace( '&amp;', '&', $this->get_return_url( $order ) ) . '";
						}, abort: function() {
							window.location.href="' . str_replace( '&amp;', '&', $order->get_cancel_order_url() ) . '";
						}
				});
				if ( ! isOpenLightbox ) {
					location.href="' . $this->payment_url . '" + code;
				}
			';

			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
				wc_enqueue_js( $js );
			} else {
				$this->woocommerce_instance()->add_inline_js( $js );
			}

			echo $html;
		} else {
			$html = '<ul class="woocommerce-error">';
				foreach ( $token['error'] as $message ) {
					$html .= '<li>' . $message . '</li>';
				}
			$html .= '</ul>';

			$html .= '<a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Click to try again', 'woocommerce-pagseguro' ) . '</a>';

			echo $html;
		}
	}

	/**
	 * Process the IPN.
	 *
	 * @return bool
	 */
	public function process_ipn_request( $data ) {

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Checking IPN request...' );
		}

		// Valid the post data.
		if ( ! isset( $data['notificationCode'] ) && ! isset( $data['notificationType'] ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Invalid IPN request: ' . print_r( $data, true ) );
			}

			return false;
		}

		// Checks the notificationType.
		if ( 'transaction' != $data['notificationType'] ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Invalid IPN request, invalid "notificationType": ' . print_r( $data, true ) );
			}

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

		// Sets the get params.
		$params = array(
			'sslverify' => false,
			'timeout'   => 60
		);

		// Gets the PagSeguro response.
		$response = wp_remote_get( $url, $params );

		// Check to see if the request was valid.
		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'WP_Error in IPN: ' . $response->get_error_message() );
			}
		} else {
			try {
				$body = new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				$body = '';

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error while parsing the PagSeguro IPN response: ' . print_r( $e->getMessage(), true ) );
				}
			}

			if ( isset( $body->code ) ) {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'PagSeguro IPN is valid! The return is: ' . print_r( $body, true ) );
				}

				return $body;
			}
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'IPN Response: ' . print_r( $response, true ) );
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

		$ipn = $this->process_ipn_request( $_POST );

		if ( $ipn ) {
			header( 'HTTP/1.1 200 OK' );
			do_action( 'valid_pagseguro_ipn_request', $ipn );
		} else {
			wp_die( __( 'PagSeguro Request Failure', 'woocommerce-pagseguro' ) );
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
				require_once plugin_dir_path( __FILE__ ) . 'class-wc-pagseguro-helpers.php';
				$helper = new WC_PagSeguro_Helpers;

				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'PagSeguro payment status for order ' . $order->get_order_number() . ' is: ' . $posted->status );
				}

				switch ( (int) $posted->status ) {
					case 1:
						$order->update_status( 'on-hold', __( 'PagSeguro: The buyer initiated the transaction, but so far the PagSeguro not received any payment information.', 'woocommerce-pagseguro' ) );

						break;
					case 2:
						$order->update_status( 'on-hold', __( 'PagSeguro: Payment under review.', 'woocommerce-pagseguro' ) );

						break;
					case 3:
						// Order details.
						if ( isset( $posted->code ) ) {
							update_post_meta(
								$order_id,
								__( 'PagSeguro Transaction ID', 'woocommerce-pagseguro' ),
								(string) $posted->code
							);
						}
						if ( isset( $posted->sender->email ) ) {
							update_post_meta(
								$order_id,
								__( 'Payer email', 'woocommerce-pagseguro' ),
								(string) $posted->sender->email
							);
						}
						if ( isset( $posted->sender->name ) ) {
							update_post_meta(
								$order_id,
								__( 'Payer name', 'woocommerce-pagseguro' ),
								(string) $posted->sender->name
							);
						}
						if ( isset( $posted->paymentMethod->type ) ) {
							update_post_meta(
								$order_id,
								__( 'Payment type', 'woocommerce-pagseguro' ),
								$helper->payment_type( (int) $posted->paymentMethod->type )
							);
						}
						if ( isset( $posted->paymentMethod->code ) ) {
							update_post_meta(
								$order_id,
								__( 'Payment method', 'woocommerce-pagseguro' ),
								$helper->payment_method( (int) $posted->paymentMethod->code )
							);
						}
						if ( isset( $posted->installmentCount ) ) {
							update_post_meta(
								$order_id,
								__( 'Installments', 'woocommerce-pagseguro' ),
								(string) $posted->installmentCount
							);
						}
						if ( isset( $posted->paymentLink ) ) {
							update_post_meta(
								$order_id,
								__( 'Payment url', 'woocommerce-pagseguro' ),
								(string) $posted->paymentLink
							);
						}

						$order->add_order_note( __( 'PagSeguro: Payment approved.', 'woocommerce-pagseguro' ) );

						// Changing the order for processing and reduces the stock.
						$order->payment_complete();

						break;
					case 4:
						$order->add_order_note( __( 'PagSeguro: Payment completed and credited to your account.', 'woocommerce-pagseguro' ) );

						break;
					case 5:
						$order->update_status( 'on-hold', __( 'PagSeguro: Payment came into dispute.', 'woocommerce-pagseguro' ) );
						$this->send_email(
							sprintf( __( 'Payment for order %s came into dispute', 'woocommerce-pagseguro' ), $order->get_order_number() ),
							__( 'Payment in dispute', 'woocommerce-pagseguro' ),
							sprintf( __( 'Order %s has been marked as on-hold, because the payment came into dispute in PagSeguro.', 'woocommerce-pagseguro' ), $order->get_order_number() )
						);

						break;
					case 6:
						$order->update_status( 'refunded', __( 'PagSeguro: Payment refunded.', 'woocommerce-pagseguro' ) );
						$this->send_email(
							sprintf( __( 'Payment for order %s refunded', 'woocommerce-pagseguro' ), $order->get_order_number() ),
							__( 'Payment refunded', 'woocommerce-pagseguro' ),
							sprintf( __( 'Order %s has been marked as refunded by PagSeguro.', 'woocommerce-pagseguro' ), $order->get_order_number() )
						);

						break;
					case 7:
						$order->update_status( 'cancelled', __( 'PagSeguro: Payment canceled.', 'woocommerce-pagseguro' ) );

						break;

					default:
						// No action xD.
						break;
				}
			} else {
				if ( 'yes' == $this->debug ) {
					$this->log->add( $this->id, 'Error: Order Key does not match with PagSeguro reference.' );
				}
			}
		}
	}

	/**
	 * Gets the admin url.
	 *
	 * @return string
	 */
	protected function admin_url() {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
			return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_pagseguro_gateway' );
		}

		return admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_PagSeguro_Gateway' );
	}

	/**
	 * Adds error message when not configured the email.
	 *
	 * @return string Error Mensage.
	 */
	public function mail_missing_message() {
		echo '<div class="error"><p><strong>' . __( 'PagSeguro Disabled', 'woocommerce-pagseguro' ) . '</strong>: ' . sprintf( __( 'You should inform your email address. %s', 'woocommerce-pagseguro' ), '<a href="' . $this->admin_url() . '">' . __( 'Click here to configure!', 'woocommerce-pagseguro' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Adds error message when not configured the token.
	 *
	 * @return string Error Mensage.
	 */
	public function token_missing_message() {
		echo '<div class="error"><p><strong>' . __( 'PagSeguro Disabled', 'woocommerce-pagseguro' ) . '</strong>: ' . sprintf( __( 'You should inform your token. %s', 'woocommerce-pagseguro' ), '<a href="' . $this->admin_url() . '">' . __( 'Click here to configure!', 'woocommerce-pagseguro' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Adds error message when an unsupported currency is used.
	 *
	 * @return string
	 */
	public function currency_not_supported_message() {
		echo '<div class="error"><p><strong>' . __( 'PagSeguro Disabled', 'woocommerce-pagseguro' ) . '</strong>: ' . sprintf( __( 'Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'woocommerce-pagseguro' ), get_woocommerce_currency() ) . '</p></div>';
	}

}
