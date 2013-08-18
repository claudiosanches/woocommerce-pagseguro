<?php
/**
 * Extends the SimpleXMLElement class to add CDATA element.
 */
class WC_PagSeguro_SimpleXML extends SimpleXMLElement {

    /**
     * Add CDATA.
     *
     * @param string $string Some string.
     */
    public function addCData( $string ) {
        $node = dom_import_simplexml( $this );
        $no = $node->ownerDocument;
        $node->appendChild( $no->createCDATASection( $string ) );
    }
}
