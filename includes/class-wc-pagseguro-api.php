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
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type' => 'application/xml;charset=UTF-8',
			)
		);

		if ( 'POST' == $method ) {
			$params = array_merge( array( 'body' => $data ), $params );
		}

		if ( ! empty( $headers ) ) {
			$params['headers'] = array_merge( $params['headers'], $headers );
		}

		$response = wp_remote_post( $url, $params );

		if ( is_wp_error( $response ) ) {
			return array( 'WP_Error: ' . $response->get_error_message() );
		}

		return $response;
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
		$helper = new WC_PagSeguro_Helpers();

		// Sets the xml.
		$xml = $this->generate_payment_xml( $order );

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Requesting token for order ' . $order->get_order_number() . ' with the following data: ' . $xml );
		}

		$url      = add_query_arg( array( 'email' => $this->gateway->email, 'token' => $this->gateway->token ), $this->get_checkout_url() );
		$response = $this->do_request( $url, 'POST', $xml );

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

				foreach ( $body->error as $key => $value ) {
					$errors[] = '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . $helper->error_message( $value->code );
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
}
