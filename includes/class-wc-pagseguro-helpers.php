<?php
/**
 * WC PagSeguro Helpers Class.
 *
 * PagSeguro payment helpers.
 *
 * @since 2.2.0
 */
class WC_PagSeguro_Helpers {

	/**
	 * Payment type name.
	 *
	 * @param  int    $value Type number.
	 *
	 * @return string        Type name.
	 */
	public function payment_type( $value ) {
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
	 * Payment method name.
	 *
	 * @param  int    $value Method number.
	 *
	 * @return string        Method name.
	 */
	public function payment_method( $value ) {
		$credit_card = __( 'Credit Card', 'woocommerce-pagseguro' );
		$billet = __( 'Billet', 'woocommerce-pagseguro' );
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
	 * Error messages.
	 *
	 * @param  int    $code Error code.
	 *
	 * @return string       Error message.
	 */
	public function error_message( $code ) {
		$error_codes = array(
			11001 => __( 'receiverEmail is required.', 'woocommerce-pagseguro' ),
			11002 => __( 'receiverEmail invalid length: {0}', 'woocommerce-pagseguro' ),
			11003 => __( 'receiverEmail invalid value.', 'woocommerce-pagseguro' ),
			11004 => __( 'Currency is required.', 'woocommerce-pagseguro' ),
			11005 => __( 'Currency invalid value: {0}', 'woocommerce-pagseguro' ),
			11006 => __( 'redirectURL invalid length: {0}', 'woocommerce-pagseguro' ),
			11007 => __( 'redirectURL invalid value: {0}', 'woocommerce-pagseguro' ),
			11008 => __( 'reference invalid length: {0}', 'woocommerce-pagseguro' ),
			11009 => __( 'senderEmail invalid length: {0}', 'woocommerce-pagseguro' ),
			11010 => __( 'senderEmail invalid value: {0}', 'woocommerce-pagseguro' ),
			11011 => __( 'senderName invalid length: {0}', 'woocommerce-pagseguro' ),
			11012 => __( 'senderName invalid value: {0}', 'woocommerce-pagseguro' ),
			11013 => __( 'senderAreaCode invalid value: {0}', 'woocommerce-pagseguro' ),
			11014 => __( 'senderPhone invalid value: {0}', 'woocommerce-pagseguro' ),
			11015 => __( 'shippingType is required.', 'woocommerce-pagseguro' ),
			11016 => __( 'shippingType invalid type: {0}', 'woocommerce-pagseguro' ),
			11017 => __( 'shippingAddressPostalCode invalid Value: {0}', 'woocommerce-pagseguro' ),
			11018 => __( 'shippingAddressStreet invalid length: {0}', 'woocommerce-pagseguro' ),
			11019 => __( 'shippingAddressNumber invalid length: {0}', 'woocommerce-pagseguro' ),
			11020 => __( 'shippingAddressComplement invalid length: {0}', 'woocommerce-pagseguro' ),
			11021 => __( 'shippingAddressDistrict invalid length: {0}', 'woocommerce-pagseguro' ),
			11022 => __( 'shippingAddressCity invalid length: {0}', 'woocommerce-pagseguro' ),
			11023 => __( 'shippingAddressState invalid value: {0}, must fit the pattern: \w\{2\} (e. g. "SP")', 'woocommerce-pagseguro' ),
			11024 => __( 'Itens invalid quantity.', 'woocommerce-pagseguro' ),
			11025 => __( 'Item Id is required.', 'woocommerce-pagseguro' ),
			11026 => __( 'Item quantity is required.', 'woocommerce-pagseguro' ),
			11027 => __( 'Item quantity out of range: {0}', 'woocommerce-pagseguro' ),
			11028 => __( 'Item amount is required. (e.g. "12.00")', 'woocommerce-pagseguro' ),
			11029 => __( 'Item amount invalid pattern: {0}. Must fit the patern: \d+.\d\{2\}', 'woocommerce-pagseguro' ),
			11030 => __( 'Item amount out of range: {0}', 'woocommerce-pagseguro' ),
			11031 => __( 'Item shippingCost invalid pattern: {0}. Must fit the patern: \d+.\d\{2\}', 'woocommerce-pagseguro' ),
			11032 => __( 'Item shippingCost out of range: {0}', 'woocommerce-pagseguro' ),
			11033 => __( 'Item description is required.', 'woocommerce-pagseguro' ),
			11034 => __( 'Item description invalid length: {0}', 'woocommerce-pagseguro' ),
			11035 => __( 'Item weight invalid Value: {0}', 'woocommerce-pagseguro' ),
			11036 => __( 'Extra amount invalid pattern: {0}. Must fit the patern: -?\d+.\d\{2\}', 'woocommerce-pagseguro' ),
			11037 => __( 'Extra amount out of range: {0}', 'woocommerce-pagseguro' ),
			11038 => __( "Invalid receiver for checkout: {0}, verify receiver's account status."),
			11039 => __( 'Malformed request XML: {0}.', 'woocommerce-pagseguro' ),
			11040 => __( 'maxAge invalid pattern: {0}. Must fit the patern: \d+', 'woocommerce-pagseguro' ),
			11041 => __( 'maxAge out of range: {0}', 'woocommerce-pagseguro' ),
			11042 => __( 'maxUses invalid pattern: {0}. Must fit the patern: \d+', 'woocommerce-pagseguro' ),
			11043 => __( 'maxUses out of range.', 'woocommerce-pagseguro' ),
			11044 => __( 'initialDate is required.', 'woocommerce-pagseguro' ),
			11045 => __( 'initialDate must be lower than allowed limit.', 'woocommerce-pagseguro' ),
			11046 => __( 'initialDate must not be older than 6 months.', 'woocommerce-pagseguro' ),
			11047 => __( 'initialDate must be lower than or equal finalDate.', 'woocommerce-pagseguro' ),
			11048 => __( 'search interval must be lower than or equal 30 days.', 'woocommerce-pagseguro' ),
			11049 => __( 'finalDate must be lower than allowed limit.', 'woocommerce-pagseguro' ),
			11050 => __( "initialDate invalid format, use 'yyyy-MM-ddTHH:mm' (eg. 2010-01-27T17:25)."),
			11051 => __( "finalDate invalid format, use 'yyyy-MM-ddTHH:mm' (eg. 2010-01-27T17:25)."),
			11052 => __( 'page invalid value.', 'woocommerce-pagseguro' ),
			11053 => __( 'maxPageResults invalid value (must be between 1 and 1000).', 'woocommerce-pagseguro' ),
			11157 => __( 'senderCPF invalid value: {0}', 'woocommerce-pagseguro' )
		);
		return in_array($code, $error_codes) ? $error_codes[$code] : __( 'An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'woocommerce-pagseguro' );
	}
}
