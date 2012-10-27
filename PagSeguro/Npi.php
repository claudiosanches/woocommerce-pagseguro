<?php
/**
 * PagSeguro Npi.
 *
 * Validates the PagSeguro payment information using Curl.
 * Class based in https://pagseguro.uol.com.br/desenvolvedor/retorno_automatico_de_dados.jhtml.
 *
 * PHP Version 5
 *
 * @category PagSeguro
 * @package PagSeguro/Nid
 */
class PagSeguro_Npi {

    /**
     * Curl timeout in seconds.
     * @var integer
     */
    private $_timeout = 20;

    /**
     * PagSeguro token.
     * @var string.
     */
    protected $token;

    /**
     * @param string $token PagSeguro user token.
     */
    function __construct( $token ) {
        $this->token = $token;
    }

    /**
     * Makes data validation.
     *
     * @return string Validation response.
     */
    public function valid() {
        $postdata = 'Comando=validar&Token=' . $this->token;

        foreach ( $_POST as $key => $value ) {
            $valued    = $this->clearStr( $value );
            $postdata .= "&$key=$valued";
        }

        return $this->verify( $postdata );
    }

    /**
     * Sanitize items.
     *
     * @param  mixed $str Post data.
     *
     * @return mixed      Sanitized item.
     */
    private function clearStr( $str ) {
        if ( !get_magic_quotes_gpc() ) {
            $str = addslashes( $str );
        }
        return $str;
    }

    /**
     * Validates the data received via curl with the PagSeguro.
     *
     * @param  array $data order items.
     *
     * @return string      returns VERIFICADO or FALSO
     */
    private function verify( $data ) {
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, 'https://pagseguro.uol.com.br/pagseguro-ws/checkout/NPI.jhtml' );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_HEADER, false );
        curl_setopt( $curl, CURLOPT_TIMEOUT, $this->_timeout );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        $result = trim( curl_exec( $curl ) );
        curl_close( $curl );
        return $result;
    }

}
