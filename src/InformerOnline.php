<?php

namespace RLWebdiensten\LaravelInformeronline;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RLWebdiensten\LaravelInformeronline\Abstracts\SalesSendMethod;
use RLWebdiensten\LaravelInformeronline\Contracts\InformerOnlineConfig;

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
    public function getRelations(): array
    {
        return $this->makeRequest("GET", "relations");
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

    public function updateContact(int $contactId): array
    {
        return $this->makeRequest("PUT", "contact/$contactId");
    }

    public function deleteContact(int $contactId): array
    {
        return $this->makeRequest("DELETE", "contact/$contactId");
    }

    // Receipts - https://api.informer.eu/docs/#/Receipts
    public function getReceipts(): array
    {
        return $this->makeRequest("GET", "receipts");
    }

    public function createReceipts(array $receiptData): array
    {
        return $this->makeRequest("POST", "receipt", $receiptData);
    }

    public function getReceipt(int $receiptId): array
    {
        return $this->makeRequest("GET", "receipt/$receiptId");
    }

    // Invoice Sales - https://api.informer.eu/docs/#/Invoices_Sales
    public function getSalesInvoices(): array
    {
        return $this->makeRequest("GET", "invoices/sales");
    }

    public function createSalesInvoice(array $invoiceData): array
    {
        return $this->makeRequest("POST", "invoices/sales", $invoiceData);
    }

    public function getSalesInvoice(int $invoiceId): array
    {
        return $this->makeRequest("GET", "invoices/sales/$invoiceId");
    }

    public function updateSalesInvoice(int $invoiceId): array
    {
        return $this->makeRequest("PUT", "invoices/sales/$invoiceId");
    }

    public function sendSalesInvoice(int $invoiceId, SalesSendMethod $method, string $email): array
    {
        return $this->makeRequest("GET", "invoices/sales/send", [
            "invoice_id" => $invoiceId,
            "method" => $method,
            "email_address" => $email,
        ]);
    }

    // Invoice Purchases - https://api.informer.eu/docs/#/Invoices_Purchases
    public function getPurchaseInvoices(): array
    {
        return $this->makeRequest("GET", "invoices/purchase");
    }

    public function getPurchaseInvoice(int $invoiceId): array
    {
        return $this->makeRequest("GET", "invoices/purchase/$invoiceId");
    }

    public function createPurchaseInvoice(array $invoiceData): array
    {
        return $this->makeRequest("POST", "invoices/purchase", $invoiceData);
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

    // Vat - https://api.informer.eu/docs/#/Vat
    public function getVat(): array
    {
        return $this->makeRequest("GET", "vat");
    }

    // Templates - https://api.informer.eu/docs/#/Templates
    public function getTemplates(): array
    {
        return $this->makeRequest("GET", "templates");
    }

    // PaymentConditions - https://api.informer.eu/docs/#/Payment_conditions
    public function getPaymentConditions(): array
    {
        return $this->makeRequest("GET", "payment-conditions");
    }

    // ---------------------------------------------------------------------------- //

    private function makeRequest(string $method, string $uri, ?array $body = null): array
    {
        try {
            $response = $this->client->request($method, $uri, array_merge($this->getClientOptions(), $this->getJsonBody($body)));
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

    private function getClientOptions(): array
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

    private function getJsonBody(?array $body = null): array
    {
        if (is_null($body)) {
            return [];
        }

        return ['json' => $body];
    }
}
