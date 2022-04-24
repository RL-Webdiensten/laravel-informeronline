<?php

namespace RLWebdiensten\LaravelInformerOnline;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RLWebdiensten\LaravelInformerOnline\Contracts\InformerOnlineConfig;
use RLWebdiensten\LaravelInformerOnline\Enums\PurchaseInvoiceStatus;
use RLWebdiensten\LaravelInformerOnline\Enums\ReceiptsStatus;
use RLWebdiensten\LaravelInformerOnline\Enums\SalesInvoiceStatus;
use RLWebdiensten\LaravelInformerOnline\Enums\SalesSendMethod;

class InformerOnline
{
    public function __construct(protected InformerOnlineConfig $config, protected Client $client)
    {
    }

    // Administration - https://api.informer.eu/docs/#/Administration
    public function getAdministrationDetails(): array
    {
        return $this->makeRequest("GET", "administration");
    }

    // Relations - https://api.informer.eu/docs/#/Relations
    public function getRelations(int $records = 100, int $page = 0, string $search = null, string $last_edit = null): array
    {
        return $this->makeRequest("GET", "relations", null, [
            'records' => $records,
            'page' => $page,
            'search' => $search,
            'last_edit' => $last_edit,
        ]);
    }

    public function getRelation(int $relationId): array
    {
        return $this->makeRequest("GET", "relation/$relationId");
    }

    public function createRelation(array $relationData): array
    {
        return $this->makeRequest("POST", "relation", $relationData);
    }

    public function updateRelation(int $relationId, array $relationData): array
    {
        return $this->makeRequest("PUT", "relation/$relationId", $relationData);
    }

    public function deleteRelation(int $relationId): array
    {
        return $this->makeRequest("DELETE", "relation/$relationId");
    }

    // Contact - https://api.informer.eu/docs/#/Relations
    public function createContact(array $contactData): array
    {
        return $this->makeRequest("POST", "contact", $contactData);
    }

    public function getContact(int $contactId): array
    {
        return $this->makeRequest("GET", "contact/$contactId");
    }

    public function updateContact(int $contactId, array $contactData): array
    {
        return $this->makeRequest("PUT", "contact/$contactId", $contactData);
    }

    public function deleteContact(int $contactId): array
    {
        return $this->makeRequest("DELETE", "contact/$contactId");
    }

    // Invoice Sales - https://api.informer.eu/docs/#/Invoices_Sales
    public function getSalesInvoices(int $records = 100, int $page = 0, ?SalesInvoiceStatus $status = null): array
    {
        return $this->makeRequest("GET", "invoices/sales", null, [
            'records' => $records,
            'page' => $page,
            'filter' => $status,
        ]);
    }

    public function getSalesInvoicesGenerator(int $records = 100, ?SalesInvoiceStatus $status = null): \Generator
    {
        $page = 0;

        do {
            $result = $this->getSalesInvoices($records, $page++, $status);
            foreach ($result as $key => $item) {
                $item['sales_invoice_id'] = $key;
                yield $key => $item;
            }
        } while (count($result) !== 0 && count($result) === $records);
    }

    public function createSalesInvoice(array $invoiceData): array
    {
        return $this->makeRequest("POST", "invoice/sales", $invoiceData);
    }

    public function getSalesInvoice(int $invoiceId): array
    {
        return $this->makeRequest("GET", "invoice/sales/$invoiceId");
    }

    public function updateSalesInvoice(int $invoiceId, array $salesInvoiceData): array
    {
        return $this->makeRequest("PUT", "invoice/sales/$invoiceId", $salesInvoiceData);
    }

    public function sendSalesInvoice(int $invoiceId, SalesSendMethod $method, string $email): array
    {
        return $this->makeRequest("GET", "invoice/sales/send", [
            "invoice_id" => $invoiceId,
            "method" => $method,
            "email_address" => $email,
        ]);
    }

    // Invoice Purchases - https://api.informer.eu/docs/#/Invoices_Purchases
    public function getPurchaseInvoices(int $records = 100, int $page = 0, ?PurchaseInvoiceStatus $status = null): array
    {
        return $this->makeRequest("GET", "invoices/purchase", null, [
            'records' => $records,
            'page' => $page,
            'filter' => $status,
        ]);
    }

    public function getPurchaseInvoice(int $invoiceId): array
    {
        return $this->makeRequest("GET", "invoice/purchase/$invoiceId");
    }

    public function createPurchaseInvoice(array $invoiceData): array
    {
        return $this->makeRequest("POST", "invoice/purchase", $invoiceData);
    }

    // Receipts - https://api.informer.eu/docs/#/Receipts
    public function getReceipts(int $records = 100, int $page = 0, ?ReceiptsStatus $status = null): array
    {
        return $this->makeRequest("GET", "receipts", null, [
            'records' => $records,
            'page' => $page,
            'filter' => $status,
        ]);
    }

    public function createReceipts(array $receiptData): array
    {
        return $this->makeRequest("POST", "receipt", $receiptData);
    }

    public function getReceipt(int $receiptId): array
    {
        return $this->makeRequest("GET", "receipt/$receiptId");
    }

    // Ledgers - https://api.informer.eu/docs/#/Ledgers
    public function getLedgers(): array
    {
        return $this->makeRequest("GET", "ledgers");
    }

    // Currencies - https://api.informer.eu/docs/#/Currencies
    public function getCurrencies(): array
    {
        return $this->makeRequest("GET", "currencies");
    }

    // Templates - https://api.informer.eu/docs/#/Templates
    public function getTemplates(): array
    {
        return $this->makeRequest("GET", "templates");
    }

    // Vat - https://api.informer.eu/docs/#/Vat
    public function getVat(): array
    {
        return $this->makeRequest("GET", "vat");
    }

    // PaymentConditions - https://api.informer.eu/docs/#/Payment_conditions
    public function getPaymentConditions(): array
    {
        return $this->makeRequest("GET", "payment-conditions");
    }

    // ---------------------------------------------------------------------------- //

    private function makeRequest(string $method, string $uri, ?array $body = null, ?array $query = null): array
    {
        try {
            $response = $this->client->request($method, $uri, $this->getClientOptions($body, $query));
            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $result = $this->convertIncomingResponseToArray($response);
            if (! $result) {
                return [];
            }

            return $result;
        } catch (GuzzleException) {
            return [];
        }
    }

    private function getClientOptions(?array $body = null, ?array $query = null): array
    {
        $options = [
            'base_uri' => 'https://' . $this->config->getBaseUri() . '/v1/',
            'headers' => [
                'Accept' => 'application/json',
                'Apikey' => $this->config->getApiKey(),
                'Securitycode' => $this->config->getSecurityCode(),
            ],
            'http_errors' => false,
            'debug' => false,
        ];

        if (! is_null($body)) {
            $options['json'] = $body;
        }
        if (! is_null($query)) {
            $options['query'] = $query;
        }

        return $options;
    }

    private function convertIncomingResponseToArray(ResponseInterface $response): ?array
    {
        try {
            $response->getBody()->rewind();
            $body = $response->getBody()->getContents();

            return (array) json_decode($body, true, 10, JSON_THROW_ON_ERROR);
        } catch (Exception) {
            return null;
        }
    }
}
