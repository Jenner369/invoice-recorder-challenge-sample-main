<?php
namespace App\Contracts;
use SimpleXMLElement;

interface IXmlVoucherService
{
    public function createXmlFromString(string $xmlContent): SimpleXMLElement;
    public function getVoucherDataFromXml(SimpleXMLElement$xml): array;
    public function processVoucherLinesFromXmlContent(SimpleXMLElement $xml, callable $callback): void;
    public function extractDetailsFromXmlContent(SimpleXMLElement $xml): array;
}