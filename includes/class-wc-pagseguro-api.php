<?php
/**
 * API class
 *
 * @package WooCommerce_PagSeguro/Classes/API
 * @version 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API.
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
	 * @param WC_PagSeguro_Gateway $gateway Payment Gateway instance.
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
	 * Get the sessions URL.
	 *
	 * @return string.
	 */
	protected function get_sessions_url() {
		return 'https://ws.' . $this->get_environment() . 'pagseguro.uol.com.br/v2/sessions';
	}

	/**
	 * Get the payment URL.
	 *
	 * @param  string $token Payment code.
	 *
	 * @return string.
	 */
	protected function get_payment_url( $token ) {
		return 'https://' . $this->get_environment() . 'pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $token;
	}

	/**
	 * Get the transactions URL.
	 *
	 * @return string.
	 */
	protected function get_transactions_url() {
		return 'https://ws.' . $this->get_environment() . 'pagseguro.uol.com.br/v2/transactions';
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
	 * Get the direct payment URL.
	 *
	 * @return string.
	 */
	public function get_direct_payment_url() {
		return 'https://stc.' . $this->get_environment() . 'pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
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
	 * Check if is localhost.
	 *
	 * @return bool
	 */
	protected function is_localhost() {
		$url  = home_url( '/' );
		$home = untrailingslashit( str_replace( array( 'https://', 'http://' ), '', $url ) );

		return in_array( $home, array( 'localhost', '127.0.0.1' ) );
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
	 * Sanitize the item description.
	 *
	 * @param  string $description Description to be sanitized.
	 *
	 * @return string
	 */
	protected function sanitize_description( $description ) {
		return sanitize_text_field( substr( $description, 0, 95 ) );
	}

	/**
	 * Get payment name by type.
	 *
	 * @param  int $value Payment Type number.
	 *
	 * @return string
	 */
	public function get_payment_name_by_type( $value ) {
		$types = array(
			1 => __( 'Credit Card', 'woocommerce-pagseguro' ),
			2 => __( 'Billet', 'woocommerce-pagseguro' ),
			3 => __( 'Bank Transfer', 'woocommerce-pagseguro' ),
			4 => __( 'PagSeguro credit', 'woocommerce-pagseguro' ),
			5 => __( 'Oi Paggo', 'woocommerce-pagseguro' ),
			7 => __( 'Account deposit', 'woocommerce-pagseguro' ),
		);

		return isset( $types[ $value ] ) ? $types[ $value ] : __( 'Unknown', 'woocommerce-pagseguro' );
	}

	/**
	 * Get payment method name.
	 *
	 * @param  int $value Payment method number.
	 *
	 * @return string
	 */
	public function get_payment_method_name( $value ) {
		$credit = __( 'Credit Card %s', 'woocommerce-pagseguro' );
		$ticket = __( 'Billet %s', 'woocommerce-pagseguro' );
		$debit  = __( 'Bank Transfer %s', 'woocommerce-pagseguro' );

		$methods = array(
			101 => sprintf( $credit, 'Visa' ),
			102 => sprintf( $credit, 'MasterCard' ),
			103 => sprintf( $credit, 'American Express' ),
			104 => sprintf( $credit, 'Diners' ),
			105 => sprintf( $credit, 'Hipercard' ),
			106 => sprintf( $credit, 'Aura' ),
			107 => sprintf( $credit, 'Elo' ),
			108 => sprintf( $credit, 'PLENOCard' ),
			109 => sprintf( $credit, 'PersonalCard' ),
			110 => sprintf( $credit, 'JCB' ),
			111 => sprintf( $credit, 'Discover' ),
			112 => sprintf( $credit, 'BrasilCard' ),
			113 => sprintf( $credit, 'FORTBRASIL' ),
			114 => sprintf( $credit, 'CARDBAN' ),
			115 => sprintf( $credit, 'VALECARD' ),
			116 => sprintf( $credit, 'Cabal' ),
			117 => sprintf( $credit, 'Mais!' ),
			118 => sprintf( $credit, 'Avista' ),
			119 => sprintf( $credit, 'GRANDCARD' ),
			201 => sprintf( $ticket, 'Bradesco' ),
			202 => sprintf( $ticket, 'Santander' ),
			301 => sprintf( $debit, 'Bradesco' ),
			302 => sprintf( $debit, 'Itaú' ),
			303 => sprintf( $debit, 'Unibanco' ),
			304 => sprintf( $debit, 'Banco do Brasil' ),
			305 => sprintf( $debit, 'Real' ),
			306 => sprintf( $debit, 'Banrisul' ),
			307 => sprintf( $debit, 'HSBC' ),
			401 => __( 'PagSeguro credit', 'woocommerce-pagseguro' ),
			501 => __( 'Oi Paggo', 'woocommerce-pagseguro' ),
			701 => __( 'Account deposit', 'woocommerce-pagseguro' ),
		);

		return isset( $methods[ $value ] ) ? $methods[ $value ] : __( 'Unknown', 'woocommerce-pagseguro' );
	}

	/**
	 * Get the paymet method.
	 *
	 * @param  string $method Payment method.
	 *
	 * @return string
	 */
	public function get_payment_method( $method ) {
		$methods = array(
			'credit-card'    => 'creditCard',
			'banking-ticket' => 'boleto',
			'bank-transfer'  => 'eft',
		);

		return isset( $methods[ $method ] ) ? $methods[ $method ] : '';
	}

	/**
	 * Get error message.
	 *
	 * @param  int $code Error code.
	 *
	 * @return string
	 */
	public function get_error_message( $code ) {
		$code = (string) $code;

		$messages = array(
			'11013' => __( 'Please enter with a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' ),
			'11014' => __( 'Please enter with a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' ),
			'53018' => __( 'Please enter with a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' ),
			'53019' => __( 'Please enter with a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' ),
			'53020' => __( 'Please enter with a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' ),
			'53021' => __( 'Please enter with a valid phone number with DDD. Example: (11) 5555-5555.', 'woocommerce-pagseguro' ),
			'11017' => __( 'Please enter with a valid zip code number.', 'woocommerce-pagseguro' ),
			'53022' => __( 'Please enter with a valid zip code number.', 'woocommerce-pagseguro' ),
			'53023' => __( 'Please enter with a valid zip code number.', 'woocommerce-pagseguro' ),
			'53053' => __( 'Please enter with a valid zip code number.', 'woocommerce-pagseguro' ),
			'53054' => __( 'Please enter with a valid zip code number.', 'woocommerce-pagseguro' ),
			'11164' => __( 'Please enter with a valid CPF number.', 'woocommerce-pagseguro' ),
			'53110' => '',
			'53111' => __( 'Please select a bank to make payment by bank transfer.', 'woocommerce-pagseguro' ),
			'53045' => __( 'Credit card holder CPF is required.', 'woocommerce-pagseguro' ),
			'53047' => __( 'Credit card holder birthdate is required.', 'woocommerce-pagseguro' ),
			'53042' => __( 'Credit card holder name is required.', 'woocommerce-pagseguro' ),
			'53049' => __( 'Credit card holder phone is required.', 'woocommerce-pagseguro' ),
			'53051' => __( 'Credit card holder phone is required.', 'woocommerce-pagseguro' ),
			'11020' => __( 'The address complement is too long, it cannot be more than 40 characters.', 'woocommerce-pagseguro' ),
			'53028' => __( 'The address complement is too long, it cannot be more than 40 characters.', 'woocommerce-pagseguro' ),
			'53029' => __( '<strong>Neighborhood</strong> is a required field.', 'woocommerce-pagseguro' ),
			'53046' => __( 'Credit card holder CPF invalid.', 'woocommerce-pagseguro' ),
			'53122' => __( 'Invalid email domain. You must use an email @sandbox.pagseguro.com.br while you are using the PagSeguro Sandbox.', 'woocommerce-pagseguro' ),
			'53081' => __( 'The customer email can not be the same as the PagSeguro account owner.', 'woocommerce-pagseguro' ),
		);

		if ( isset( $messages[ $code ] ) ) {
			return $messages[ $code ];
		}

		return __( 'An error has occurred while processing your payment, please review your data and try again. Or contact us for assistance.', 'woocommerce-pagseguro' );
	}

	/**
	 * Get the available payment methods.
	 *
	 * @return array
	 */
	protected function get_available_payment_methods() {
		$methods = array();

		if ( 'yes' == $this->gateway->tc_credit ) {
			$methods[] = 'credit-card';
		}

		if ( 'yes' == $this->gateway->tc_transfer ) {
			$methods[] = 'bank-transfer';
		}

		if ( 'yes' == $this->gateway->tc_ticket ) {
			$methods[] = 'banking-ticket';
		}

		return $methods;
	}

	/**
	 * Do requests in the PagSeguro API.
	 *
	 * @param  string $url      URL.
	 * @param  string $method   Request method.
	 * @param  array  $data     Request data.
	 * @param  array  $headers  Request headers.
	 *
	 * @return array            Request response.
	 */
	protected function do_request( $url, $method = 'POST', $data = array(), $headers = array() ) {
		$params = array(
			'method'  => $method,
			'timeout' => 60,
		);

		if ( 'POST' == $method && ! empty( $data ) ) {
			$params['body'] = $data;
		}

		if ( ! empty( $headers ) ) {
			$params['headers'] = $headers;
		}

		return wp_safe_remote_post( $url, $params );
	}

	/**
	 * Safe load XML.
	 *
	 * @param  string $source  XML source.
	 * @param  int    $options DOMDpocment options.
	 *
	 * @return SimpleXMLElement|bool
	 */
	protected function safe_load_xml( $source, $options = 0 ) {
		$old = null;

		if ( '<' !== substr( $source, 0, 1 ) ) {
			return false;
		}

		if ( function_exists( 'libxml_disable_entity_loader' ) ) {
			$old = libxml_disable_entity_loader( true );
		}

		$dom    = new DOMDocument();
		$return = $dom->loadXML( $source, $options );

		if ( ! is_null( $old ) ) {
			libxml_disable_entity_loader( $old );
		}

		if ( ! $return ) {
			return false;
		}

		if ( isset( $dom->doctype ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'Unsafe DOCTYPE Detected while XML parsing' );
			}

			return false;
		}

		return simplexml_import_dom( $dom );
	}

	/**
	 * Get order items.
	 *
	 * @param  WC_Order $order Order data.
	 *
	 * @return array           Items list, extra amount and shipping cost.
	 */
	protected function get_order_items( $order ) {
		$items         = array();
		$extra_amount  = 0;
		$shipping_cost = 0;

		// Force only one item.
		if ( 'yes' == $this->gateway->send_only_total ) {
			$items[] = array(
				'description' => $this->sanitize_description( sprintf( __( 'Order %s', 'woocommerce-pagseguro' ), $order->get_order_number() ) ),
				'amount'      => $this->money_format( $order->get_total() ),
				'quantity'    => 1,
			);
		} else {

			// Products.
			if ( 0 < count( $order->get_items() ) ) {
				foreach ( $order->get_items() as $order_item ) {
					if ( $order_item['qty'] ) {
						$item_total = $order->get_item_total( $order_item, false );
						if ( 0 >= (float) $item_total ) {
							continue;
						}

						$item_name = $order_item['name'];

						if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0', '<' ) ) {
							if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.4.0', '<' ) ) {
								$item_meta = new WC_Order_Item_Meta( $order_item['item_meta'] );
							} else {
								$item_meta = new WC_Order_Item_Meta( $order_item );
							}

							if ( $meta = $item_meta->display( true, true ) ) {
								$item_name .= ' - ' . $meta;
							}
						}

						$items[] = array(
							'description' => $this->sanitize_description( str_replace( '&ndash;', '-', $item_name ) ),
							'amount'      => $this->money_format( $item_total ),
							'quantity'    => $order_item['qty'],
						);
					}
				}
			}

			// Fees.
			if ( 0 < count( $order->get_fees() ) ) {
				foreach ( $order->get_fees() as $fee ) {
					if ( 0 >= (float) $fee['line_total'] ) {
						continue;
					}

					$items[] = array(
						'description' => $this->sanitize_description( $fee['name'] ),
						'amount'      => $this->money_format( $fee['line_total'] ),
						'quantity'    => 1,
					);
				}
			}

			// Taxes.
			if ( 0 < count( $order->get_taxes() ) ) {
				foreach ( $order->get_taxes() as $tax ) {
					$tax_total = $tax['tax_amount'] + $tax['shipping_tax_amount'];
					if ( 0 >= (float) $tax_total ) {
						continue;
					}

					$items[] = array(
						'description' => $this->sanitize_description( $tax['label'] ),
						'amount'      => $this->money_format( $tax_total ),
						'quantity'    => 1,
					);
				}
			}

			// Shipping Cost.
			if ( 0 < $order->get_total_shipping() ) {
				$shipping_cost = $this->money_format( $order->get_total_shipping() );
			}

			// Discount.
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '<' ) ) {
				if ( 0 < $order->get_order_discount() ) {
					$extra_amount = '-' . $this->money_format( $order->get_order_discount() );
				}
			}
		}

		return array(
			'items'         => $items,
			'extra_amount'  => $extra_amount,
			'shipping_cost' => $shipping_cost,
		);
	}

	/**
	 * Get the checkout xml.
	 *
	 * @param WC_Order $order Order data.
	 * @param array    $posted Posted data.
	 *
	 * @return string
	 */
	protected function get_checkout_xml( $order, $posted ) {
		$data    = $this->get_order_items( $order );
		$ship_to = isset( $posted['ship_to_different_address'] ) ? true : false;

		// Creates the checkout xml.
		$xml = new WC_PagSeguro_XML( '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><checkout></checkout>' );
		$xml->add_currency( get_woocommerce_currency() );

		// WooCommerce 3.0 or later.
		if ( method_exists( $order, 'get_id' ) ) {
			$xml->add_reference( $this->gateway->invoice_prefix . $order->get_id() );
			$xml->add_sender_data( $order );
			$xml->add_shipping_data( $order, $ship_to, $data['shipping_cost'] );
		} else {
			$xml->add_reference( $this->gateway->invoice_prefix . $order->id );
			$xml->add_legacy_sender_data( $order );
			$xml->add_legacy_shipping_data( $order, $ship_to, $data['shipping_cost'] );
		}

		$xml->add_items( $data['items'] );
		$xml->add_extra_amount( $data['extra_amount'] );

		// Checks if is localhost... PagSeguro not accept localhost urls!
		if ( ! in_array( $this->is_localhost(), array( 'localhost', '127.0.0.1' ) ) ) {
			$xml->add_redirect_url( $this->gateway->get_return_url( $order ) );
			$xml->add_notification_url( WC()->api_request_url( 'WC_PagSeguro_Gateway' ) );
		}

		$xml->add_max_uses( 1 );
		$xml->add_max_age( 120 );

		// Filter the XML.
		$xml = apply_filters( 'woocommerce_pagseguro_checkout_xml', $xml, $order );

		return $xml->render();
	}

	/**
	 * Get the direct payment xml.
	 *
	 * @param WC_Order $order Order data.
	 * @param array    $posted Posted data.
	 *
	 * @return string
	 */
	protected function get_payment_xml( $order, $posted ) {
		$data    = $this->get_order_items( $order );
		$ship_to = isset( $posted['ship_to_different_address'] ) ? true : false;
		$method  = isset( $posted['pagseguro_payment_method'] ) ? $this->get_payment_method( $posted['pagseguro_payment_method'] ) : '';
		$hash    = isset( $posted['pagseguro_sender_hash'] ) ? sanitize_text_field( $posted['pagseguro_sender_hash'] ) : '';

		// Creates the payment xml.
		$xml = new WC_PagSeguro_XML( '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><payment></payment>' );
		$xml->add_mode( 'default' );
		$xml->add_method( $method );
		$xml->add_currency( get_woocommerce_currency() );
		if ( ! in_array( $this->is_localhost(), array( 'localhost', '127.0.0.1' ) ) ) {
			$xml->add_notification_url( WC()->api_request_url( 'WC_PagSeguro_Gateway' ) );
		}
		$xml->add_items( $data['items'] );
		$xml->add_extra_amount( $data['extra_amount'] );

		// WooCommerce 3.0 or later.
		if ( method_exists( $order, 'get_id' ) ) {
			$xml->add_reference( $this->gateway->invoice_prefix . $order->get_id() );
			$xml->add_sender_data( $order, $hash );
			$xml->add_shipping_data( $order, $ship_to, $data['shipping_cost'] );
		} else {
			$xml->add_reference( $this->gateway->invoice_prefix . $order->id );
			$xml->add_legacy_sender_data( $order, $hash );
			$xml->add_legacy_shipping_data( $order, $ship_to, $data['shipping_cost'] );
		}

		// Items related to the payment method.
		if ( 'creditCard' == $method ) {
			$credit_card_token = isset( $posted['pagseguro_credit_card_hash'] ) ? sanitize_text_field( $posted['pagseguro_credit_card_hash'] ) : '';
			$installment       = array(
				'quantity' => isset( $posted['pagseguro_card_installments'] ) ? absint( $posted['pagseguro_card_installments'] ) : '',
				'value'    => isset( $posted['pagseguro_installment_value'] ) ? $this->money_format( $posted['pagseguro_installment_value'] ) : '',
			);
			$holder_data       = array(
				'name'       => isset( $posted['pagseguro_card_holder_name'] ) ? sanitize_text_field( $posted['pagseguro_card_holder_name'] ) : '',
				'cpf'        => isset( $posted['pagseguro_card_holder_cpf'] ) ? sanitize_text_field( $posted['pagseguro_card_holder_cpf'] ) : '',
				'birth_date' => isset( $posted['pagseguro_card_holder_birth_date'] ) ? sanitize_text_field( $posted['pagseguro_card_holder_birth_date'] ) : '',
				'phone'      => isset( $posted['pagseguro_card_holder_phone'] ) ? sanitize_text_field( $posted['pagseguro_card_holder_phone'] ) : '',
			);

			// WooCommerce 3.0 or later.
			if ( method_exists( $order, 'get_id' ) ) {
				$xml->add_credit_card_data( $order, $credit_card_token, $installment, $holder_data );
			} else {
				$xml->add_legacy_credit_card_data( $order, $credit_card_token, $installment, $holder_data );
			}
		} elseif ( 'eft' == $method ) {
			$bank_name = isset( $posted['pagseguro_bank_transfer'] ) ? sanitize_text_field( $posted['pagseguro_bank_transfer'] ) : '';
			$xml->add_bank_data( $bank_name );
		}

		// Filter the XML.
		$xml = apply_filters( 'woocommerce_pagseguro_payment_xml', $xml, $order );

		return $xml->render();
	}

	/**
	 * Do checkout request.
	 *
	 * @param  WC_Order $order  Order data.
	 * @param  array    $posted Posted data.
	 *
	 * @return array
	 */
	public function do_checkout_request( $order, $posted ) {
		// Sets the xml.
		$xml = $this->get_checkout_xml( $order, $posted );

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Requesting token for order ' . $order->get_order_number() . ' with the following data: ' . $xml );
		}

		$url      = add_query_arg( array( 'email' => $this->gateway->get_email(), 'token' => $this->gateway->get_token() ), $this->get_checkout_url() );
		$response = $this->do_request( $url, 'POST', $xml, array( 'Content-Type' => 'application/xml;charset=UTF-8' ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'WP_Error in generate payment token: ' . $response->get_error_message() );
			}
		} else if ( 401 === $response['response']['code'] ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'Invalid token and/or email settings!' );
			}

			return array(
				'url'   => '',
				'data'  => '',
				'error' => array( __( 'Too bad! The email or token from the PagSeguro are invalids my little friend!', 'woocommerce-pagseguro' ) ),
			);
		} else {
			try {
				libxml_disable_entity_loader( true );
				$body = $this->safe_load_xml( $response['body'], LIBXML_NOCDATA );
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
					'url'   => $this->get_payment_url( $token ),
					'token' => $token,
					'error' => '',
				);
			}

			if ( isset( $body->error ) ) {
				$errors = array();

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'Failed to generate the PagSeguro Payment Token: ' . print_r( $response, true ) );
				}

				foreach ( $body->error as $error_key => $error ) {
					if ( $message = $this->get_error_message( $error->code ) ) {
						$errors[] = '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . $message;
					}
				}

				return array(
					'url'   => '',
					'token' => '',
					'error' => $errors,
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
			'error' => array( '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'woocommerce-pagseguro' ) ),
		);
	}

	/**
	 * Do payment request.
	 *
	 * @param  WC_Order $order  Order data.
	 * @param  array    $posted Posted data.
	 *
	 * @return array
	 */
	public function do_payment_request( $order, $posted ) {
		$payment_method = isset( $posted['pagseguro_payment_method'] ) ? $posted['pagseguro_payment_method'] : '';

		/**
		 * Validate if has selected a payment method.
		 */
		if ( ! in_array( $payment_method, $this->get_available_payment_methods() ) ) {
			return array(
				'url'   => '',
				'data'  => '',
				'error' => array( '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' .  __( 'Please, select a payment method.', 'woocommerce-pagseguro' ) ),
			);
		}

		// Sets the xml.
		$xml = $this->get_payment_xml( $order, $posted );

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Requesting direct payment for order ' . $order->get_order_number() . ' with the following data: ' . $xml );
		}

		$url      = add_query_arg( array( 'email' => $this->gateway->get_email(), 'token' => $this->gateway->get_token() ), $this->get_transactions_url() );
		$response = $this->do_request( $url, 'POST', $xml, array( 'Content-Type' => 'application/xml;charset=UTF-8' ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'WP_Error in requesting the direct payment: ' . $response->get_error_message() );
			}
		} else if ( 401 === $response['response']['code'] ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'The user does not have permissions to use the PagSeguro Transparent Checkout!' );
			}

			return array(
				'url'   => '',
				'data'  => '',
				'error' => array( __( 'You are not allowed to use the PagSeguro Transparent Checkout. Looks like you neglected to installation guide of this plugin. This is not pretty, do you know?', 'woocommerce-pagseguro' ) ),
			);
		} else {
			try {
				$data = $this->safe_load_xml( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				$data = '';

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'Error while parsing the PagSeguro response: ' . print_r( $e->getMessage(), true ) );
				}
			}

			if ( isset( $data->code ) ) {
				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'PagSeguro direct payment created successfully!' );
				}

				return array(
					'url'   => $this->gateway->get_return_url( $order ),
					'data'  => $data,
					'error' => '',
				);
			}

			if ( isset( $data->error ) ) {
				$errors = array();

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'An error occurred while generating the PagSeguro direct payment: ' . print_r( $response, true ) );
				}

				foreach ( $data->error as $error_key => $error ) {
					if ( $message = $this->get_error_message( $error->code ) ) {
						$errors[] = '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . $message;
					}
				}

				return array(
					'url'   => '',
					'data'  => '',
					'error' => $errors,
				);
			}
		}

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'An error occurred while generating the PagSeguro direct payment: ' . print_r( $response, true ) );
		}

		// Return error message.
		return array(
			'url'   => '',
			'data'  => '',
			'error' => array( '<strong>' . __( 'PagSeguro', 'woocommerce-pagseguro' ) . '</strong>: ' . __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'woocommerce-pagseguro' ) ),
		);
	}

	/**
	 * Process the IPN.
	 *
	 * @param  array $data IPN data.
	 *
	 * @return bool|SimpleXMLElement
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
		$url      = add_query_arg( array( 'email' => $this->gateway->get_email(), 'token' => $this->gateway->get_token() ), $this->get_notification_url() . esc_attr( $data['notificationCode'] ) );
		$response = $this->do_request( $url, 'GET' );

		// Check to see if the request was valid.
		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'WP_Error in IPN: ' . $response->get_error_message() );
			}
		} else {
			try {
				$body = $this->safe_load_xml( $response['body'], LIBXML_NOCDATA );
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

	/**
	 * Get session ID.
	 *
	 * @return string
	 */
	public function get_session_id() {

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Requesting session ID...' );
		}

		$url      = add_query_arg( array( 'email' => $this->gateway->get_email(), 'token' => $this->gateway->get_token() ), $this->get_sessions_url() );
		$response = $this->do_request( $url, 'POST' );

		// Check to see if the request was valid.
		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->gateway->debug ) {
				$this->gateway->log->add( $this->gateway->id, 'WP_Error requesting session ID: ' . $response->get_error_message() );
			}
		} else {
			try {
				$session = $this->safe_load_xml( $response['body'], LIBXML_NOCDATA );
			} catch ( Exception $e ) {
				$session = '';

				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'Error while parsing the PagSeguro session response: ' . print_r( $e->getMessage(), true ) );
				}
			}

			if ( isset( $session->id ) ) {
				if ( 'yes' == $this->gateway->debug ) {
					$this->gateway->log->add( $this->gateway->id, 'PagSeguro session is valid! The return is: ' . print_r( $session, true ) );
				}

				return (string) $session->id;
			}
		}

		if ( 'yes' == $this->gateway->debug ) {
			$this->gateway->log->add( $this->gateway->id, 'Session Response: ' . print_r( $response, true ) );
		}

		return false;
	}
}
