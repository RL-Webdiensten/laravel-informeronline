<?php

namespace RLWebdiensten\LaravelInformerOnline;

use Exception;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RLWebdiensten\LaravelInformerOnline\Contracts\InformerOnlineConfig;
use RLWebdiensten\LaravelInformerOnline\Enums\PurchaseInvoiceStatus;
use RLWebdiensten\LaravelInformerOnline\Enums\ReceiptsStatus;
use RLWebdiensten\LaravelInformerOnline\Enums\SalesInvoiceStatus;
use RLWebdiensten\LaravelInformerOnline\Enums\SalesSendMethod;
use RLWebdiensten\LaravelInformerOnline\Exceptions\ConnectionFailedException;
use RLWebdiensten\LaravelInformerOnline\Exceptions\InvalidResponseException;

class InformerOnline
{
    public function __construct(protected InformerOnlineConfig $config, protected Client $client)
    {
    }

    // Administration - https://api.informer.eu/docs/#/Administration
    public function getAdministrationDetails(): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "administration",
            field: "administration"
        );
    }

    // Relations - https://api.informer.eu/docs/#/Relations
    public function getRelations(int $records = 100, int $page = 0, string $search = null, string $last_edit = null): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "relations",
            query: [
                'records' => $records,
                'page' => $page,
                'search' => $search,
                'last_edit' => $last_edit,
            ],
            field: "relation"
        );
    }

    public function getRelationsGenerator(int $records = 100, string $search = ''): Generator
    {
        $page = 0;

        do {
            $result = $this->getRelations($records, $page++, $search);
            foreach ($result as $key => $item) {
                $item['relation_id'] = $key;
                yield $key => $item;
            }
        } while (count($result) !== 0 && count($result) === $records);
    }

    public function getRelation(int $relationId): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "relation/$relationId",
            field: "relation"
        );
    }

    public function createRelation(array $relationData): int
    {
        $response = $this->makeRequest(
            method: "POST",
            uri: "relation",
            body: $relationData
        );

        if (! isset($response['id'])) {
            throw new InvalidResponseException();
        }

        return $response['id'];
    }

    public function updateRelation(int $relationId, array $relationData): int
    {
        $response = $this->makeRequest(
            method: "PUT",
            uri: "relation/$relationId",
            body: $relationData
        );

        if (! isset($response['id'])) {
            throw new InvalidResponseException();
        }

        return $response['id'];
    }

    public function deleteRelation(int $relationId): bool
    {
        $response = $this->makeRequest(
            method: "DELETE",
            uri: "relation/$relationId"
        );

        if (isset($response['sucess'])) {
            return true;
        }

        return false;
    }

    // Contact - https://api.informer.eu/docs/#/Relations
    public function createContact(array $contactData): int
    {
        $response = $this->makeRequest(
            method: "POST",
            uri: "contact",
            body: $contactData
        );

        if (! isset($response['id'])) {
            throw new InvalidResponseException();
        }

        return $response['id'];
    }

    public function getContact(int $contactId): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "contact/$contactId",
            field: "contact"
        );
    }

    public function updateContact(int $contactId, array $contactData): int
    {
        $response = $this->makeRequest(
            method: "PUT",
            uri: "contact/$contactId",
            body: $contactData
        );

        if (! isset($response['id'])) {
            throw new InvalidResponseException();
        }

        return $response['id'];
    }

    public function deleteContact(int $contactId): bool
    {
        $response = $this->makeRequest(
            method: "DELETE",
            uri: "contact/$contactId"
        );

        if (! isset($response['sucess'])) {
            return false;
        }

        return true;
    }

    // Invoice Sales - https://api.informer.eu/docs/#/Invoices_Sales
    public function getSalesInvoices(int $records = 100, int $page = 0, ?SalesInvoiceStatus $status = null): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "invoices/sales",
            query: [
                'records' => $records,
                'page' => $page,
                'filter' => $status,
            ],
            field: "sales"
        );
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

    public function createSalesInvoice(array $invoiceData): int
    {
        $response = $this->makeRequest(
            method: "POST",
            uri: "invoice/sales",
            body: $invoiceData
        );

        if (! isset($response['invoice_id'])) {
            throw new InvalidResponseException();
        }

        return $response['invoice_id'];
    }

    public function getSalesInvoice(int $invoiceId): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "invoice/sales/$invoiceId",
            field: "sales"
        );
    }

    public function updateSalesInvoice(int $invoiceId, array $salesInvoiceData): int
    {
        $response = $this->makeRequest(
            method: "PUT",
            uri: "invoice/sales/$invoiceId",
            body: $salesInvoiceData
        );

        if (! isset($response['invoice_id'])) {
            throw new InvalidResponseException();
        }

        return $response['invoice_id'];
    }

    public function sendSalesInvoice(int $invoiceId, SalesSendMethod $method, string $email): bool
    {
        $response = $this->makeRequest(
            method: "GET",
            uri: "invoice/sales/send",
            body: [
                "invoice_id" => $invoiceId,
                "method" => $method,
                "email_address" => $email,
            ],
        );

        if (! isset($response['message'])) {
            return false;
        }

        return true;
    }

    // Invoice Purchases - https://api.informer.eu/docs/#/Invoices_Purchases
    public function getPurchaseInvoices(int $records = 100, int $page = 0, ?PurchaseInvoiceStatus $status = null): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "invoices/purchase",
            query: [
                'records' => $records,
                'page' => $page,
                'filter' => $status,
            ],
            field: "purchase"
        );
    }

    public function getPurchaseInvoice(int $invoiceId): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "invoice/purchase/$invoiceId",
            field: "purchase"
        );
    }

    public function createPurchaseInvoice(array $invoiceData): int
    {
        $response = $this->makeRequest(
            method: "POST",
            uri: "invoice/purchase",
            body: $invoiceData
        );

        if (! isset($response['invoice_id'])) {
            throw new InvalidResponseException();
        }

        return $response['invoice_id'];
    }

    // Receipts - https://api.informer.eu/docs/#/Receipts
    public function getReceipts(int $records = 100, int $page = 0, ?ReceiptsStatus $status = null): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "receipts",
            query: [
                'records' => $records,
                'page' => $page,
                'filter' => $status,
            ],
            field: "receipts"
        );
    }

    public function createReceipts(array $receiptData): array
    {
        return $this->makeRequest(
            method: "POST",
            uri: "receipt",
            body: $receiptData
        );
    }

    public function getReceipt(int $receiptId): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "receipt/$receiptId",
            field: "receipts"
        );
    }

    // Ledgers - https://api.informer.eu/docs/#/Ledgers
    public function getLedgers(): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "ledgers",
            field: "ledgers"
        );
    }

    // Currencies - https://api.informer.eu/docs/#/Currencies
    public function getCurrencies(): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "currencies",
            field: "currencies"
        );
    }

    // Templates - https://api.informer.eu/docs/#/Templates
    public function getTemplates(): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "templates",
            field: "templates"
        );
    }

    // Vat - https://api.informer.eu/docs/#/Vat
    public function getVat(): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "vat",
            field: "vat"
        );
    }

    // PaymentConditions - https://api.informer.eu/docs/#/Payment_conditions
    public function getPaymentConditions(): array
    {
        return $this->makeRequest(
            method: "GET",
            uri: "payment-conditions",
            field: "paymentconditions"
        );
    }

    // ---------------------------------------------------------------------------- //
    private function makeRequest(string $method, string $uri, ?array $body = null, ?array $query = null, ?string $field = null): array
    {
        try {
            $response = $this->client->request($method, $uri, $this->getClientOptions($body, $query));
            if ($response->getStatusCode() !== 200) {
                // TODO: throw errors for error codes
                throw new InvalidResponseException();
            }

            $result = $this->convertIncomingResponseToArray($response);
            if (! $result || ! is_array($result)) {
                throw new InvalidResponseException();
            }

            if (isset($result['error'])) {
                throw new InvalidResponseException($result['error']);
            }

            if (! $field) {
                return $result;
            }

            if (! isset($result[$field])) {
                throw new InvalidResponseException();
            }

            return $result[$field];
        } catch (GuzzleException $e) {
            throw new ConnectionFailedException($e->getMessage());
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
            throw new InvalidResponseException();
        }
    }
}
