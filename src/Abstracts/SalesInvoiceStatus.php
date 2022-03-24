<?php

namespace RLWebdiensten\LaravelInformerOnline\Abstracts;

class SalesInvoiceStatus
{
    public const SALES_INVOICE_STATUS_OPEN = 'open';
    public const SALES_INVOICE_STATUS_EXPIRED = 'expired';
    public const SALES_INVOICE_STATUS_COLLECT = 'collect';
    public const SALES_INVOICE_STATUS_DRAFT = 'draft';

    public static function in_array($status): bool
    {
        return in_array($status, [self::SALES_INVOICE_STATUS_EXPIRED, self::SALES_INVOICE_STATUS_COLLECT, self::SALES_INVOICE_STATUS_DRAFT], true);
    }
}
