<?php
/**
 * WC PagSeguro API Class.
 */
class WC_PagSeguro_API {

	/**
	 * Gateway class.
	 *
	 * @var WC_PagSeguro_Gateway
	 */
	protected $gateway;

	/**
	 * Constructor.
	 *
	 * @param WC_PagSeguro_Gateway $gateway
	 */
	public function __construct( $gateway = null ) {
		$this->gateway = $gateway;
	}

	/**
	 * Get the API environment.
	 *
	 * @return string
	 */
	protected function get_environment() {
		return ( 'yes' == $this->gateway->sandbox ) ? 'sandbox.' : '';
	}

	/**
	 * Get the checkout URL.
	 *
	 * @return string.
	 */
	protected function get_checkout_url() {
		return 'https://ws.' . $this->get_environment() . 'pagseguro.uol.com.br/v2/checkout';
	}

	/**
	 * Get the payment URL.
	 *
	 * @return string.
	 */
	protected function get_payment_url() {
		return 'https://' . $this->get_environment() . 'pagseguro.uol.com.br/v2/checkout/payment.html?code=';
	}

	/**
	 * Get the lightbox URL.
	 *
	 * @return string.
	 */
	public function get_lightbox_url() {
		return 'https://stc.' . $this->get_environment() . 'pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js';
	}

	/**
	 * Get the notification URL.
	 *
	 * @return string.
	 */
	protected function get_notification_url() {
		return 'https://ws.' . $this->get_environment() . 'pagseguro.uol.com.br/v2/transactions/notifications/';
	}

	/**
	 * Money format.
	 *
	 * @param  int/float $value Value to fix.
	 *
	 * @return float            Fixed value.
	 */
	protected function money_format( $value ) {
		return number_format( $value, 2, '.', '' );
	}

	/**
	 * Get payment name by type.
	 *
	 * @param  int    $value Payment Type number.
	 *
	 * @return string        Payment name.
	 */
	public function get_payment_name_by_type( $value ) {
		$types = array(
			1 => __( 'Credit Card', 'woocommerce-pagseguro' ),
			2 => __( 'Billet', 'woocommerce-pagseguro' ),
			3 => __( 'Online Debit', 'woocommerce-pagseguro' ),
			4 => __( 'PagSeguro credit', 'woocommerce-pagseguro' ),
			5 => __( 'Oi Paggo', 'woocommerce-pagseguro' ),
			7 => __( 'Account deposit', 'woocommerce-pagseguro' )
		);

		if ( isset( $types[ $value ] ) ) {
			return $types[ $value ];
		} else {
			return __( 'Unknown', 'woocommerce-pagseguro' );
		}
	}

	/**
	 * Get payment method name.
	 *
	 * @param  int    $value Payment method number.
	 *
	 * @return string        Payment method name.
	 */
	public function get_payment_method_name( $value ) {
		$credit_card  = __( 'Credit Card', 'woocommerce-pagseguro' );
		$billet       = __( 'Billet', 'woocommerce-pagseguro' );
		$online_debit = __( 'Online Debit', 'woocommerce-pagseguro' );

		$methods = array(
			101 => $credit_card . ' ' . 'Visa',
			102 => $credit_card . ' ' . 'MasterCard',
			103 => $credit_card . ' ' . 'American Express',
			104 => $credit_card . ' ' . 'Diners',
			105 => $credit_card . ' ' . 'Hipercard',
			106 => $credit_card . ' ' . 'Aura',
			107 => $credit_card . ' ' . 'Elo',
			108 => $credit_card . ' ' . 'PLENOCard',
			109 => $credit_card . ' ' . 'PersonalCard',
			110 => $credit_card . ' ' . 'JCB',
			111 => $credit_card . ' ' . 'Discover',
			112 => $credit_card . ' ' . 'BrasilCard',
			113 => $credit_card . ' ' . 'FORTBRASIL',
			114 => $credit_card . ' ' . 'CARDBAN',
			115 => $credit_card . ' ' . 'VALECARD',
			116 => $credit_card . ' ' . 'Cabal',
			117 => $credit_card . ' ' . 'Mais!',
			118 => $credit_card . ' ' . 'Avista',
			119 => $credit_card . ' ' . 'GRANDCARD',
			201 => $billet . ' ' . 'Bradesco',
			202 => $billet . ' ' . 'Santander',
			301 => $online_debit . ' ' . 'Bradesco',
			302 => $online_debit . ' ' . 'Itaú',
			303 => $online_debit . ' ' . 'Unibanco',
			304 => $online_debit . ' ' . 'Banco do Brasil',
			305 => $online_debit . ' ' . 'Real',
			306 => $online_debit . ' ' . 'Banrisul',
			307 => $online_debit . ' ' . 'HSBC',
			401 => __( 'PagSeguro credit', 'woocommerce-pagseguro' ),
			501 => __( 'Oi Paggo', 'woocommerce-pagseguro' ),
			701 => __( 'Account deposit', 'woocommerce-pagseguro' )
		);

		if ( isset( $methods[ $value ] ) ) {
			return $methods[ $value ];
		} else {
			return __( 'Unknown', 'woocommerce-pagseguro' );
		}
	}

	/**
	 * Get error message.
	 *
	 * @param  int    $code Error code.
	 *
	 * @return string       Error message.
	 */
	public function get_error_message( $code ) {
		switch ( $code ) {
			case 11013:
			case 11014:
				return __( 'Please enter a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' );
				break;
			case 11017:
				return __( 'Please enter a valid zip code number.', 'woocommerce-pagseguro' );
				break;
			case 11164:
				return __( 'Please enter a valid CPF number.', 'woocommerce-pagseguro' );
				break;

			default:
				return __( 'An error has occurred while processing your payment, please review your data and try again. Or contact us for assistance.', 'woocommerce-pagseguro' );
				break;
		}
	}

	/**
	 * Do requests in the PagSeguro API.
	 *
	 * @param  string $endpoint API Endpoint.
	 * @param  string $method   Request method.
	 * @param  array  $data     Request data.
	 * @param  array  $headers  Request headers.
	 *
	 * @return array            Request response.
	 */
	protected function do_request( $url, $method = 'POST', $data = array(), $headers = array() ) {
		$params = array(
			'method'    => $method,
			'sslverify' => false,
			'timeout'   => 60
		);

		if ( 'POST' == $method ) {
			$params['body'] = $data;
		}

		if ( ! empty( $headers ) ) {
			$params['headers'] = $headers;
		}

		return wp_remote_post( $url, $params );
	}

	/**
	 * Generate the payment xml.
	 *
	 * @param object  $order Order data.
	 *
	 * @return string        Payment xml.
	 */
	protected function generate_payment_xml( $order ) {
		global $woocommerce;

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$notification_url = WC()->api_request_url( 'WC_PagSeguro_Gateway' );
		} else {
			$notification_url = $woocommerce->api_request_url( 'WC_PagSeguro_Gateway' );
		}

		// Creates the payment xml.
		$xml = new WC_PagSeguro_SimpleXML( '<?xml version="1.0" encoding="utf-8" standalone="yes" ?><checkout></checkout>' );

		// Currency.
		$xml->addChild( 'currency', get_woocommerce_currency() );

		// Reference.
		$xml->addChild( 'reference' )->addCData( $this->gateway->invoice_prefix . $order->id );

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

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$shipping_total = $this->money_format( $order->get_total_shipping() );
		} else {
			$shipping_total = $this->money_format( $order->get_shipping() );
		}

		// If prices include tax or have order discounts, send the whole order as a single item.
		if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) || $order->get_order_discount() > 0 ) {

			// Discount.
			if ( $order->get_order_discount() > 0 ) {
				$xml->addChild( 'extraAmount', '-' . $this->money_format( $order->get_order_discount() ) );
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
			$item->addChild( 'amount', $this->money_format( $order->get_total() - $shipping_total - $order->get_shipping_tax() + $order->get_order_discount() ) );
			$item->addChild( 'quantity', 1 );

			if ( ( $shipping_total + $order->get_shipping_tax() ) > 0 ) {
				$shipping->addChild( 'cost', $this->money_format( $shipping_total + $order->get_shipping_tax(), 2, '.', '' ) );
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
						$item->addChild( 'amount', $this->money_format( $order->get_item_total( $order_item, false ) ) );
						$item->addChild( 'quantity', $order_item['qty'] );
					}
				}
			}

			// Shipping Cost item.
			if ( $shipping_total > 0 ) {
				$shipping->addChild( 'cost', $this->money_format( $shipping_total, 2, '.', '' ) );
			}

			// Extras Amount.
			$xml->addChild( 'extraAmount', $this->money_format( $order->get_total_tax() ) );
		}

		// Checks if is localhost. PagSeguro not accept localhost urls!
		if ( ! in_array( $_SERVER['HTTP_HOST'], array( 'localhost', '127.0.0.1' ) ) ) {
			$xml->addChild( 'redirectURL' )->addCData( $this->get_return_url( $order ) );
			$xml->addChild( 'notificationURL' )->addCData( $notification_url );
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
	 * Do payment request.
	 *
	 * @param object $order Order data.
	 *
	 * @return array
	 */
	public function do_payment_request( $order ) {
		// Sets the xml.
		$xml = $this->generate_payment_xml( $order );

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Requesting token for order ' . $order->get_order_number() . ' with the following data: ' . $xml );
		}

		$url      = add_query_arg( array( 'email' => $this->gateway->email, 'token' => $this->gateway->token ), $this->get_checkout_url() );
		$response = $this->do_request( $url, 'POST', $xml, array( 'Content-Type' => 'application/xml;charset=UTF-8' ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'WP_Error in generate payment token: ' . $response->get_error_message() );
			}
		} else {
			try {
				$body = @new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				$body = '';

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'Error while parsing the PagSeguro response: ' . print_r( $e->getMessage(), true ) );
				}
			}

			if ( isset( $body->code ) ) {
				$token = (string) $body->code;

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'PagSeguro Payment Token created with success! The Token is: ' . $token );
				}

				return array(
					'url'   => $this->get_payment_url() . $token,
					'token' => $token,
					'error' => ''
				);
			}

			if ( isset( $body->error ) ) {
				$errors = array();

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'Failed to generate the PagSeguro Payment Token: ' . print_r( $response, true ) );
				}

				foreach ( $body->error as $error_key => $error ) {
					$errors[] = '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . $this->get_error_message( $error->code );
				}

				return array(
					'url'   => '',
					'token' => '',
					'error' => $errors
				);
			}
		}

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Error generating the PagSeguro payment token: ' . print_r( $response, true ) );
		}

		// Return error message.
		return array(
			'url'   => '',
			'token' => '',
			'error' => array( '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'woocommerce-pagseguro' ) )
		);
	}

	/**
	 * Process the IPN.
	 *
	 * @return bool|SimpleXmlElement
	 */
	public function process_ipn_request( $data ) {

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Checking IPN request...' );
		}

		// Valid the post data.
		if ( ! isset( $data['notificationCode'] ) && ! isset( $data['notificationType'] ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'Invalid IPN request: ' . print_r( $data, true ) );
			}

			return false;
		}

		// Checks the notificationType.
		if ( 'transaction' != $data['notificationType'] ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'Invalid IPN request, invalid "notificationType": ' . print_r( $data, true ) );
			}

			return false;
		}

		// Gets the PagSeguro response.
		$url      = add_query_arg( array( 'email' => $this->gateway->email, 'token' => $this->gateway->token ), $this->get_notification_url() . esc_attr( $data['notificationCode'] ) );
		$response = do_request( $url, 'GET' );

		// Check to see if the request was valid.
		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'WP_Error in IPN: ' . $response->get_error_message() );
			}
		} else {
			try {
				$body = @new SimpleXmlElement( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				$body = '';

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'Error while parsing the PagSeguro IPN response: ' . print_r( $e->getMessage(), true ) );
				}
			}

			if ( isset( $body->code ) ) {
				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'PagSeguro IPN is valid! The return is: ' . print_r( $body, true ) );
				}

				return $body;
			}
		}

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'IPN Response: ' . print_r( $response, true ) );
		}

		return false;
	}

}
