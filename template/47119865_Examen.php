<?php
// Paso 1: Definición de la clase
class GeneradorXML {

    // Paso 3: Definir función pública
    function CrearXMLFactura($factura, $items) {
        // Paso 6: Comentario sobre propósito
        // Clase para generar XML de Factura electrónica en formato UBL

        // Paso 5: Nueva instancia DOMDocument
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = false; // Paso 7: desactivar formato
        $doc->preserveWhiteSpace = true; // Paso 8: preservar espacios

        // Paso 10: Comentario explicativo
        // Construcción del string XML base
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"';
        $xml .= ' xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"';
        $xml .= ' xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"';
        $xml .= ' xmlns:ds="http://www.w3.org/2000/09/xmldsig#"';
        $xml .= ' xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">';

        // Paso 17: UBLExtensions
        $xml .= '<ext:UBLExtensions>';
        $xml .= '<ext:UBLExtension>';
        $xml .= '<ext:ExtensionContent />'; // Paso 19
        $xml .= '</ext:UBLExtension>';
        $xml .= '</ext:UBLExtensions>';

        // Paso 22: Versión UBL y Personalización
        $xml .= '<cbc:UBLVersionID>2.1</cbc:UBLVersionID>';
        $xml .= '<cbc:CustomizationID>2.0</cbc:CustomizationID>';

        // Paso 24–27: Datos de la factura
        $xml .= '<cbc:ID>' . $factura["serie"] . '-' . $factura["correlativo"] . '</cbc:ID>';
        $xml .= '<cbc:IssueDate>' . $factura["fecha"] . '</cbc:IssueDate>';
        $xml .= '<cbc:IssueTime>' . $factura["hora"] . '</cbc:IssueTime>';
        $xml .= '<cbc:DueDate>' . $factura["vencimiento"] . '</cbc:DueDate>';
        $xml .= '<cbc:InvoiceTypeCode listID="0101">' . $factura["tipo"] . '</cbc:InvoiceTypeCode>';

        // Paso 29–30: Nota y moneda
        $xml .= '<cbc:Note><![CDATA[' . $factura["total_letras"] . ']]></cbc:Note>';
        $xml .= '<cbc:DocumentCurrencyCode>' . $factura["moneda"] . '</cbc:DocumentCurrencyCode>';

        // Paso 31–47: Firma digital
        $xml .= '<cac:Signature>';
        $xml .= '<cbc:ID>' . $factura["ruc"] . '</cbc:ID>';
        $xml .= '<cbc:Note><![CDATA[' . $factura["nombre_comercial"] . ']]></cbc:Note>';
        $xml .= '<cac:SignatoryParty>';
        $xml .= '<cac:PartyIdentification><cbc:ID>' . $factura["ruc"] . '</cbc:ID></cac:PartyIdentification>';
        $xml .= '<cac:PartyName><cbc:Name><![CDATA[' . $factura["razon_social"] . ']]></cbc:Name></cac:PartyName>';
        $xml .= '</cac:SignatoryParty>';
        $xml .= '<cac:DigitalSignatureAttachment><cac:ExternalReference><cbc:URI>#SIGN-1</cbc:URI></cac:ExternalReference></cac:DigitalSignatureAttachment>';
        $xml .= '</cac:Signature>';

        // Paso 48–74: Emisor
        $xml .= '<cac:AccountingSupplierParty>';
        $xml .= '<cac:Party>';
        $xml .= '<cac:PartyIdentification><cbc:ID schemeID="6">' . $factura["ruc"] . '</cbc:ID></cac:PartyIdentification>';
        $xml .= '<cac:PartyName><cbc:Name><![CDATA[' . $factura["nombre_comercial"] . ']]></cbc:Name></cac:PartyName>';
        $xml .= '<cac:PartyLegalEntity>';
        $xml .= '<cbc:RegistrationName><![CDATA[' . $factura["razon_social"] . ']]></cbc:RegistrationName>';
        $xml .= '<cac:RegistrationAddress>';
        $xml .= '<cbc:ID>' . $factura["ubigeo"] . '</cbc:ID>';
        $xml .= '<cbc:AddressTypeCode>0000</cbc:AddressTypeCode>';
        $xml .= '<cbc:CitySubdivisionName>NONE</cbc:CitySubdivisionName>';
        $xml .= '<cbc:CityName>' . $factura["provincia"] . '</cbc:CityName>';
        $xml .= '<cbc:CountrySubentity>' . $factura["departamento"] . '</cbc:CountrySubentity>';
        $xml .= '<cbc:District>' . $factura["distrito"] . '</cbc:District>';
        $xml .= '<cac:AddressLine><cbc:Line><![CDATA[' . $factura["direccion"] . ']]></cbc:Line></cac:AddressLine>';
        $xml .= '<cac:Country><cbc:IdentificationCode>PE</cbc:IdentificationCode></cac:Country>';
        $xml .= '</cac:RegistrationAddress>';
        $xml .= '</cac:PartyLegalEntity>';
        $xml .= '</cac:Party>';
        $xml .= '</cac:AccountingSupplierParty>';

        // Paso 75–92: Cliente
        $xml .= '<cac:AccountingCustomerParty>';
        $xml .= '<cac:Party>';
        $xml .= '<cac:PartyIdentification><cbc:ID schemeID="' . $factura["tipo_doc_cliente"] . '">' . $factura["doc_cliente"] . '</cbc:ID></cac:PartyIdentification>';
        $xml .= '<cac:PartyLegalEntity>';
        $xml .= '<cbc:RegistrationName><![CDATA[' . $factura["razon_social_cliente"] . ']]></cbc:RegistrationName>';
        $xml .= '<cac:RegistrationAddress>';
        $xml .= '<cac:AddressLine><cbc:Line><![CDATA[' . $factura["direccion_cliente"] . ']]></cbc:Line></cac:AddressLine>';
        $xml .= '<cac:Country><cbc:IdentificationCode>PE</cbc:IdentificationCode></cac:Country>';
        $xml .= '</cac:RegistrationAddress>';
        $xml .= '</cac:PartyLegalEntity>';
        $xml .= '</cac:Party>';
        $xml .= '</cac:AccountingCustomerParty>';

        // Paso 143–147: Totales monetarios
        $xml .= '<cac:LegalMonetaryTotal>';
        $xml .= '<cbc:LineExtensionAmount currencyID="' . $factura["moneda"] . '">' . $factura["subtotal"] . '</cbc:LineExtensionAmount>';
        $xml .= '<cbc:TaxInclusiveAmount currencyID="' . $factura["moneda"] . '">' . $factura["total"] . '</cbc:TaxInclusiveAmount>';
        $xml .= '<cbc:PayableAmount currencyID="' . $factura["moneda"] . '">' . $factura["total"] . '</cbc:PayableAmount>';
        $xml .= '</cac:LegalMonetaryTotal>';

        // Paso 148–182: Items
        foreach ($items as $i => $item) {
            $xml .= '<cac:InvoiceLine>';
            $xml .= '<cbc:ID>' . ($i + 1) . '</cbc:ID>';
            $xml .= '<cbc:InvoicedQuantity unitCode="' . $item["unidad"] . '">' . $item["cantidad"] . '</cbc:InvoicedQuantity>';
            $xml .= '<cbc:LineExtensionAmount currencyID="' . $factura["moneda"] . '">' . $item["subtotal"] . '</cbc:LineExtensionAmount>';
            $xml .= '<cac:Item><cbc:Description><![CDATA[' . $item["descripcion"] . ']]></cbc:Description>';
            $xml .= '<cac:SellersItemIdentification><cbc:ID>' . $item["codigo"] . '</cbc:ID></cac:SellersItemIdentification></cac:Item>';
            $xml .= '<cac:Price><cbc:PriceAmount currencyID="' . $factura["moneda"] . '">' . $item["precio"] . '</cbc:PriceAmount></cac:Price>';
            $xml .= '</cac:InvoiceLine>';
        }

        // Paso 184: Cierre del nodo principal
        $xml .= '</Invoice>';

        // Paso 185–186: Cargar al DOM y guardar
        $doc->loadXML($xml);
        $nombreArchivo = "F" . $factura["serie"] . "-" . $factura["correlativo"] . ".xml";
        $doc->save($nombreArchivo);

        return $nombreArchivo;
    }
}