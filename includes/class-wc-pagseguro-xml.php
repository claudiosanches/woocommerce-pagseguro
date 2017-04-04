<?php
/**
 * WooCommerce PagSeguro SimpleXMLElement class
 *
 * @package WooCommerce_PagSeguro/Classes/XML
 * @version 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extends the SimpleXMLElement class to add CDATA element.
 */
class WC_PagSeguro_XML extends SimpleXMLElement {

	/**
	 * Extract numbers from a string.
	 *
	 * @param  string $string String where will be extracted numbers.
	 *
	 * @return string
	 */
	protected function get_numbers( $string ) {
		return preg_replace( '([^0-9])', '', $string );
	}

	/**
	 * Add CDATA.
	 *
	 * @param string $string Some string.
	 */
	public function add_cdata( $string ) {
		$node = dom_import_simplexml( $this );
		$no   = $node->ownerDocument;

		$node->appendChild( $no->createCDATASection( trim( $string ) ) );
	}

	/**
	 * Add currency.
	 *
	 * @param string $currency Currency code.
	 */
	public function add_currency( $currency ) {
		$this->addChild( 'currency', $currency );
	}

	/**
	 * Add payment mode.
	 *
	 * @param string $mode Payment mode.
	 */
	public function add_mode( $mode = 'default' ) {
		$this->addChild( 'mode', $mode );
	}

	/**
	 * Add payment method.
	 *
	 * @param string $method Payment method (creditCard, boleto or eft).
	 */
	public function add_method( $method = 'creditCard' ) {
		$this->addChild( 'method', $method );
	}

	/**
	 * Add reference.
	 *
	 * @param string $reference Payment reference.
	 */
	public function add_reference( $reference ) {
		$this->addChild( 'reference' )->add_cdata( $reference );
	}

	/**
	 * Add receiver email.
	 *
	 * @param string $receiver_email Receiver email.
	 */
	public function add_receiver_email( $receiver_email ) {
		$receiver = $this->addChild( 'receiver' );
		$receiver->addChild( 'email', $receiver_email );
	}

	/**
	 * Add CPF.
	 *
	 * @param string $number Document number.
	 * @param SimpleXMLElement $xml Data.
	 */
	protected function add_cpf( $number, $xml ) {
		$documents = $xml->addChild( 'documents' );
		$document  = $documents->addChild( 'document' );
		$document->addChild( 'type', 'CPF' );
		$document->addChild( 'value', $this->get_numbers( $number ) );
	}

	/**
	 * Add CNPJ.
	 *
	 * @param string $number Document number.
	 * @param SimpleXMLElement $xml Data.
	 */
	protected function add_cnpj( $number, $xml ) {
		$documents = $xml->addChild( 'documents' );
		$document  = $documents->addChild( 'document' );
		$document->addChild( 'type', 'CNPJ' );
		$document->addChild( 'value', $this->get_numbers( $number ) );
	}

	/**
	 * Legacy - Add sender data.
	 *
	 * @param WC_Order $order Order data.
	 * @param string   $hash  Sender hash.
	 */
	public function add_legacy_sender_data( $order, $hash = '' ) {
		$name   = $order->billing_first_name . ' ' . $order->billing_last_name;
		$sender = $this->addChild( 'sender' );
		$sender->addChild( 'email' )->add_cdata( $order->billing_email );

		$wcbcf_settings = get_option( 'wcbcf_settings' );
		$wcbcf_settings = isset( $wcbcf_settings['person_type'] ) ? intval( $wcbcf_settings['person_type'] ) : 0;

		if ( ( 0 === $wcbcf_settings || 2 === $wcbcf_settings ) && ! empty( $order->billing_cpf ) ) {
			$this->add_cpf( $order->billing_cpf, $sender );
		} else if ( ( 0 === $wcbcf_settings || 3 === $wcbcf_settings ) && ! empty( $order->billing_cnpj ) ) {
			$name = $order->billing_company;
			$this->add_cnpj( $order->billing_cnpj, $sender );
		} else if ( ! empty( $order->billing_persontype ) ) {
			if ( 1 == $order->billing_persontype && ! empty( $order->billing_cpf ) ) {
				$this->add_cpf( $order->billing_cpf, $sender );
			} else if ( 2 == $order->billing_persontype && ! empty( $order->billing_cnpj ) ) {
				$name = $order->billing_company;
				$this->add_cnpj( $order->billing_cnpj, $sender );
			}
		}

		$sender->addChild( 'name' )->add_cdata( $name );

		if ( isset( $order->billing_phone ) && ! empty( $order->billing_phone ) ) {
			$phone_number = $this->get_numbers( $order->billing_phone );
			$phone        = $sender->addChild( 'phone' );
			$phone->addChild( 'areaCode', substr( $phone_number, 0, 2 ) );
			$phone->addChild( 'number', substr( $phone_number, 2 ) );
		}

		if ( '' != $hash ) {
			$sender->addChild( 'hash', $hash );
		}
	}

	/**
	 * Add sender data.
	 *
	 * @param WC_Order $order Order data.
	 * @param string   $hash  Sender hash.
	 */
	public function add_sender_data( $order, $hash = '' ) {
		$name   = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$sender = $this->addChild( 'sender' );
		$sender->addChild( 'email' )->add_cdata( $order->get_billing_email() );

		$wcbcf_settings = get_option( 'wcbcf_settings' );
		$wcbcf_settings = isset( $wcbcf_settings['person_type'] ) ? intval( $wcbcf_settings['person_type'] ) : 0;

		if ( ( 0 === $wcbcf_settings || 2 === $wcbcf_settings ) && '' !== $order->get_meta( '_billing_cpf' ) ) {
			$this->add_cpf( $order->get_meta( '_billing_cpf' ), $sender );
		} else if ( ( 0 === $wcbcf_settings || 3 === $wcbcf_settings ) && '' !== $order->get_meta( '_billing_cnpj' ) ) {
			$name = $order->get_billing_company();
			$this->add_cnpj( $order->get_meta( '_billing_cnpj' ), $sender );
		} else if ( '' !== $order->get_meta( '_billing_persontype' ) ) {
			if ( 1 === intval( $order->get_meta( '_billing_persontype' ) ) && '' !== $order->get_meta( '_billing_cpf' ) ) {
				$this->add_cpf( $order->get_meta( '_billing_cpf' ), $sender );
			} else if ( 2 === intval( $order->get_meta( '_billing_persontype' ) ) && '' !== $order->get_meta( '_billing_cnpj' ) ) {
				$name = $order->get_billing_company();
				$this->add_cnpj( $order->get_meta( '_billing_cnpj' ), $sender );
			}
		}

		$sender->addChild( 'name' )->add_cdata( $name );

		if ( '' !== $order->get_billing_phone() ) {
			$phone_number = $this->get_numbers( $order->get_billing_phone() );
			$phone        = $sender->addChild( 'phone' );
			$phone->addChild( 'areaCode', substr( $phone_number, 0, 2 ) );
			$phone->addChild( 'number', substr( $phone_number, 2 ) );
		}

		if ( '' != $hash ) {
			$sender->addChild( 'hash', $hash );
		}
	}

	/**
	 * Legacy - Add shipping data.
	 *
	 * @param WC_Order $order         Order data.
	 * @param bool     $ship_to       Ship to (true = shipping address, false = billing address).
	 * @param float    $shipping_cost Shipping cost.
	 */
	public function add_legacy_shipping_data( $order, $ship_to = false, $shipping_cost = 0 ) {
		$type = ( $ship_to ) ? 'shipping' : 'billing';

		$shipping = $this->addChild( 'shipping' );
		$shipping->addChild( 'type', 3 );

		if ( isset( $order->{ $type . '_postcode' } ) && ! empty( $order->{ $type . '_postcode' } ) ) {
			$address = $shipping->addChild( 'address' );
			$address->addChild( 'street' )->add_cdata( $order->{ $type . '_address_1' } );

			if ( isset( $order->{ $type . '_number' } ) ) {
				$address->addChild( 'number', $order->{ $type . '_number' } );
			}

			if ( ! empty( $order->{ $type . '_address_2' } ) ) {
				$address->addChild( 'complement' )->add_cdata( $order->{ $type . '_address_2' } );
			}

			if ( isset( $order->{ $type . '_neighborhood' } ) ) {
				$address->addChild( 'district' )->add_cdata( $order->{ $type . '_neighborhood' } );
			}

			$address->addChild( 'postalCode', $this->get_numbers( $order->{ $type . '_postcode' } ) );
			$address->addChild( 'city' )->add_cdata( $order->{ $type . '_city' } );
			$address->addChild( 'state', $order->{ $type . '_state' } );
			$address->addChild( 'country', 'BRA' );
		}

		$shipping->addChild( 'cost', $shipping_cost );
	}

	/**
	 * Add shipping data.
	 *
	 * @param WC_Order $order         Order data.
	 * @param bool     $ship_to       Ship to (true = shipping address, false = billing address).
	 * @param float    $shipping_cost Shipping cost.
	 */
	public function add_shipping_data( $order, $ship_to = false, $shipping_cost = 0 ) {
		$type = ( $ship_to ) ? 'shipping' : 'billing';

		$shipping = $this->addChild( 'shipping' );
		$shipping->addChild( 'type', 3 );

		if ( '' !== $order->{ 'get_' . $type . '_postcode' }() ) {
			$address = $shipping->addChild( 'address' );
			$address->addChild( 'street' )->add_cdata( $order->{ 'get_' . $type . '_address_1' }() );

			if ( '' !== $order->get_meta( '_' . $type . '_number' ) ) {
				$address->addChild( 'number', $order->get_meta( '_' . $type . '_number' ) );
			}

			if ( '' !== $order->{ 'get_' . $type . '_address_2' }() ) {
				$address->addChild( 'complement' )->add_cdata( $order->{ 'get_' . $type . '_address_2' }() );
			}

			if ( '' !== $order->get_meta( '_' . $type . '_neighborhood' ) ) {
				$address->addChild( 'district' )->add_cdata( $order->get_meta( '_' . $type . '_neighborhood' ) );
			}

			$address->addChild( 'postalCode', $this->get_numbers( $order->{ 'get_' . $type . '_postcode' }() ) );
			$address->addChild( 'city' )->add_cdata( $order->{ 'get_' . $type . '_city' }() );
			$address->addChild( 'state', $order->{ 'get_' . $type . '_state' }() );
			$address->addChild( 'country', 'BRA' );
		}

		$shipping->addChild( 'cost', $shipping_cost );
	}

	/**
	 * Add order items.
	 *
	 * @param array $_items Order items.
	 */
	public function add_items( $_items ) {
		$items = $this->addChild( 'items' );

		foreach ( $_items as $id => $_item ) {
			$item = $items->addChild( 'item' );

			$item->addChild( 'id', $id + 1 );
			$item->addChild( 'description' )->add_cdata( $_item['description'] );
			$item->addChild( 'amount', $_item['amount'] );
			$item->addChild( 'quantity', $_item['quantity'] );
		}
	}

	/**
	 * Add extra amount.
	 *
	 * @param float $extra_amount Extra amount.
	 */
	public function add_extra_amount( $extra_amount = 0 ) {
		if ( 0 != $extra_amount ) {
			$this->addChild( 'extraAmount', $extra_amount );
		}
	}

	/**
	 * Legacy - Add credit card data.
	 *
	 * @param WC_Order $order           Order data.
	 * @param string   $credit_card_token Credit card token.
	 * @param array    $installment_data  Installment data (quantity and value).
	 * @param array    $holder_data       Holder data (name, cpf, birth_date and phone).
	 */
	public function add_legacy_credit_card_data( $order, $credit_card_token, $installment_data, $holder_data ) {
		$credit_card = $this->addChild( 'creditCard' );

		$credit_card->addChild( 'token', $credit_card_token );

		$installment = $credit_card->addChild( 'installment' );
		$installment->addChild( 'quantity', $installment_data['quantity'] );
		$installment->addChild( 'value', $installment_data['value'] );

		$holder = $credit_card->addChild( 'holder' );
		$holder->addChild( 'name' )->add_cdata( $holder_data['name'] );
		$documents = $holder->addChild( 'documents' );
		$document = $documents->addChild( 'document' );
		$document->addChild( 'type', 'CPF' );
		$document->addChild( 'value', $this->get_numbers( $holder_data['cpf'] ) );
		$holder->addChild( 'birthDate', str_replace( ' ', '', $holder_data['birth_date'] ) );
		$phone_number = $this->get_numbers( $holder_data['phone'] );
		$phone = $holder->addChild( 'phone' );
		$phone->addChild( 'areaCode', substr( $phone_number, 0, 2 ) );
		$phone->addChild( 'number', substr( $phone_number, 2 ) );

		$billing_address = $credit_card->addChild( 'billingAddress' );
		$billing_address->addChild( 'street' )->add_cdata( $order->billing_address_1 );
		if ( isset( $order->billing_number ) ) {
			$billing_address->addChild( 'number', $order->billing_number );
		}
		if ( ! empty( $order->billing_address_2 ) ) {
			$billing_address->addChild( 'complement' )->add_cdata( $order->billing_address_2 );
		}
		if ( isset( $order->billing_neighborhood ) ) {
			$billing_address->addChild( 'district' )->add_cdata( $order->billing_neighborhood );
		}
		$billing_address->addChild( 'city' )->add_cdata( $order->billing_city );
		$billing_address->addChild( 'state', $order->billing_state );
		$billing_address->addChild( 'country', 'BRA' );
		$billing_address->addChild( 'postalCode', $this->get_numbers( $order->billing_postcode ) );
	}

	/**
	 * Add credit card data.
	 *
	 * @param WC_Order $order           Order data.
	 * @param string   $credit_card_token Credit card token.
	 * @param array    $installment_data  Installment data (quantity and value).
	 * @param array    $holder_data       Holder data (name, cpf, birth_date and phone).
	 */
	public function add_credit_card_data( $order, $credit_card_token, $installment_data, $holder_data ) {
		$credit_card = $this->addChild( 'creditCard' );

		$credit_card->addChild( 'token', $credit_card_token );

		$installment = $credit_card->addChild( 'installment' );
		$installment->addChild( 'quantity', $installment_data['quantity'] );
		$installment->addChild( 'value', $installment_data['value'] );

		$holder = $credit_card->addChild( 'holder' );
		$holder->addChild( 'name' )->add_cdata( $holder_data['name'] );
		$documents = $holder->addChild( 'documents' );
		$document = $documents->addChild( 'document' );
		$document->addChild( 'type', 'CPF' );
		$document->addChild( 'value', $this->get_numbers( $holder_data['cpf'] ) );
		$holder->addChild( 'birthDate', str_replace( ' ', '', $holder_data['birth_date'] ) );
		$phone_number = $this->get_numbers( $holder_data['phone'] );
		$phone = $holder->addChild( 'phone' );
		$phone->addChild( 'areaCode', substr( $phone_number, 0, 2 ) );
		$phone->addChild( 'number', substr( $phone_number, 2 ) );

		$billing_address = $credit_card->addChild( 'billingAddress' );
		$billing_address->addChild( 'street' )->add_cdata( $order->get_billing_address_1() );
		if ( '' !== $order->get_meta( '_billing_number' ) ) {
			$billing_address->addChild( 'number', $order->get_meta( '_billing_number' ) );
		}
		if ( '' !== $order->get_billing_address_2() ) {
			$billing_address->addChild( 'complement' )->add_cdata( $order->get_billing_address_2() );
		}
		if ( '' !== $order->get_meta( '_billing_neighborhood' ) ) {
			$billing_address->addChild( 'district' )->add_cdata( $order->get_meta( '_billing_neighborhood' ) );
		}
		$billing_address->addChild( 'city' )->add_cdata( $order->get_billing_city() );
		$billing_address->addChild( 'state', $order->get_billing_state() );
		$billing_address->addChild( 'country', 'BRA' );
		$billing_address->addChild( 'postalCode', $this->get_numbers( $order->get_billing_postcode() ) );
	}

	/**
	 * Add bank data.
	 *
	 * @param string $bank_name bradesco, itau, bancodobrasil, banrisul or hsbc.
	 */
	public function add_bank_data( $bank_name ) {
		$bank = $this->addChild( 'bank' );
		$bank->addChild( 'name', $bank_name );
	}

	/**
	 * Add redirect URL.
	 *
	 * @param string $redirect_url URL to redirect from PagSeguro.
	 */
	public function add_redirect_url( $redirect_url ) {
		$this->addChild( 'redirectURL' )->add_cdata( $redirect_url );
	}

	/**
	 * Add notification URL.
	 *
	 * @param string $notification_url URL to PagSeguro send the payment status notification.
	 */
	public function add_notification_url( $notification_url ) {
		$this->addChild( 'notificationURL' )->add_cdata( $notification_url );
	}

	/**
	 * Add max uses.
	 *
	 * @param int $max Max uses.
	 */
	public function add_max_uses( $max = 1 ) {
		$this->addChild( 'maxUses', $max );
	}

	/**
	 * Add max age.
	 *
	 * @param int $max Max age.
	 */
	public function add_max_age( $max = 120 ) {
		$this->addChild( 'maxAge', $max );
	}

	/**
	 * Render the formated XML.
	 *
	 * @return string
	 */
	public function render() {
		$node = dom_import_simplexml( $this );
		$dom  = $node->ownerDocument;
		$dom->formatOutput = true;

		return $dom->saveXML();
	}
}
