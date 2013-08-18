<?php
/**
 * WC PagSeguro Helpers Class.
 *
 * PagSeguro payment helpers.
 *
 * @since  2.0.0
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
        switch ( $value ) {
            case 1:
                $type = __( 'Credit Card', 'wcpagseguro' );
                break;
            case 2:
                $type = __( 'Billet', 'wcpagseguro' );
                break;
            case 3:
                $type = __( 'Online Debit', 'wcpagseguro' );
                break;
            case 4:
                $type = __( 'PagSeguro credit', 'wcpagseguro' );
                break;
            case 4:
                $type = __( 'Oi Paggo', 'wcpagseguro' );
                break;

            default:
                $type = __( 'Unknown', 'wcpagseguro' );
                break;
        }

        return $type;
    }


    public function payment_method( $value ) {
        $credit_card = __( 'Credit Card', 'wcpagseguro' );
        $billet = __( 'Billet', 'wcpagseguro' );
        $online_debit = __( 'Online Debit', 'wcpagseguro' );

        switch ( $value ) {
            case 101:
                $method = $credit_card . ' ' . 'Visa';
                break;
            case 102:
                $method = $credit_card . ' ' . 'MasterCard';
                break;
            case 103:
                $method = $credit_card . ' ' . 'American Express';
                break;
            case 104:
                $method = $credit_card . ' ' . 'Diners';
                break;
            case 105:
                $method = $credit_card . ' ' . 'Hipercard';
                break;
            case 106:
                $method = $credit_card . ' ' . 'Aura';
                break;
            case 107:
                $method = $credit_card . ' ' . 'Elo';
                break;
            case 108:
                $method = $credit_card . ' ' . 'PLENOCard';
                break;
            case 109:
                $method = $credit_card . ' ' . 'PersonalCard';
                break;
            case 110:
                $method = $credit_card . ' ' . 'JCB';
                break;
            case 111:
                $method = $credit_card . ' ' . 'Discover';
                break;
            case 112:
                $method = $credit_card . ' ' . 'BrasilCard';
                break;
            case 113:
                $method = $credit_card . ' ' . 'FORTBRASIL';
                break;
            case 114:
                $method = $credit_card . ' ' . 'CARDBAN';
                break;
            case 115:
                $method = $credit_card . ' ' . 'VALECARD';
                break;
            case 116:
                $method = $credit_card . ' ' . 'Cabal';
                break;
            case 117:
                $method = $credit_card . ' ' . 'Mais!';
                break;
            case 118:
                $method = $credit_card . ' ' . 'Avista';
                break;
            case 119:
                $method = $credit_card . ' ' . 'GRANDCARD';
                break;
            case 201:
                $method = $billet . ' ' . 'Bradesco';
                break;
            case 202:
                $method = $billet . ' ' . 'Santander';
                break;
            case 301:
                $method = $online_debit . ' ' . 'Bradesco';
                break;
            case 302:
                $method = $online_debit . ' ' . 'Itaú';
                break;
            case 303:
                $method = $online_debit . ' ' . 'Unibanco';
                break;
            case 304:
                $method = $online_debit . ' ' . 'Banco do Brasil';
                break;
            case 305:
                $method = $online_debit . ' ' . 'Real';
                break;
            case 306:
                $method = $online_debit . ' ' . 'Banrisul';
                break;
            case 307:
                $method = $online_debit . ' ' . 'HSBC';
                break;
            case 401:
                $method = __( 'PagSeguro credit', 'wcpagseguro' );
                break;
            case 501:
                $method = __( 'Oi Paggo', 'wcpagseguro' );
                break;

            default:
                $method = __( 'Unknown', 'wcpagseguro' );
                break;
        }

        return $method;
    }
}
