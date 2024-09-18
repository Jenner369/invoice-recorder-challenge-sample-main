<?php
namespace App\Services;
use App\Contracts\IXmlVoucherService;
use SimpleXMLElement;

class XmlVoucherService implements IXmlVoucherService
{

    /**
     * Create an XML instance from a string content.
     * @param string $xmlContent
     * @return SimpleXMLElement
     */
    public function createXmlFromString(string $xmlContent): SimpleXMLElement
    {
        return new SimpleXMLElement($xmlContent);
    }


    /**
     * Extracts the issuer, receiver, total amount and details from the XML content
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function getVoucherDataFromXml(SimpleXMLElement $xml): array 
    {

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $details = $this->extractDetailsFromXmlContent($xml);

        return [
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            ...$details,
        ];
    }

    /**
     * Processes the voucher lines from the XML content
     * @param SimpleXMLElement $xml
     * @param callable $callback
     */
    public function processVoucherLinesFromXmlContent(SimpleXMLElement $xml, callable $callback): void
    {
        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {

            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];
            $callback([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
            
        }
    }

    /**
     * Extracts the series, number, voucher type and currency from the XML content
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function extractDetailsFromXmlContent(SimpleXMLElement $xml): array
    {  
        
        $id = (string) $xml->xpath('//cbc:ID')[0];
        [$series, $number] = explode('-', $id);
        $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

        return [
            'series' => $series,
            'number' => $number,
            'voucher_type' => $voucherType,
            'currency' => $currency,
        ];
    }

}    