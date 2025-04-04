<?php declare(strict_types=1);

namespace App\Api\Responses;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use XMLWriter;

class XmlResponse extends Response
{
    public function __construct(array $data, int $status = 200)
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('api-response');
        if (isset($data['meta'])) {
            $this->writeMetadata($xml, $data['meta']);
        }
        if (isset($data['error'])) {
            $this->writeErrors($xml, $data['error']);
        }
        if (isset($data['data'])) {
            $this->writeData($xml, $data['data']);
        }
        $xml->endElement();
        $xml->endDocument();

        $body = new Stream('php://temp', 'wb+');
        $body->write($xml->outputMemory());
        $body->rewind();

        parent::__construct($body, $status, [
            'Content-Type' => 'application/xml'
        ]);
    }

    private function writeData(XMLWriter $xml, array $data): void
    {
        $xml->startElement('data');
        $this->arrayToXml($xml, $data);
        $xml->endElement();
    }

    private function writeMetadata(XMLWriter $xml, $data): void
    {
        $xml->startElement('metadata');
        $this->arrayToXml($xml, $data);
        $xml->endElement();
    }

    private function arrayToXml(XMLWriter $xml, array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';
            } else {
                $key = preg_replace('~[^0-9a-z]+~', '-', strtolower($key));
            }
            if (is_array($value)) {
                $xml->startElement($key);
                $this->arrayToXml($xml, $value);
                $xml->endElement();
            } else {
                $xml->writeElement($key, (string)$value);
            }
        }
    }

    private function writeErrors(XMLWriter $xml, string $error)
    {
        $xml->writeElement('error', $error);
    }
}