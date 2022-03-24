<?php

namespace RLWebdiensten\LaravelInformerOnline\Abstracts;

class PurchaseInvoiceStatus
{
    public const SALES_INVOICE_STATUS_OPEN = 'open';
    public const SALES_INVOICE_STATUS_EXPIRED = 'expired';
    public const SALES_INVOICE_STATUS_PROCESS = 'process';
    public const SALES_INVOICE_STATUS_PAID = 'paid';
    public const SALES_INVOICE_STATUS_APPROVE = 'approve';

    public static function in_array($status): bool
    {
        return in_array($status, [self::SALES_INVOICE_STATUS_OPEN, self::SALES_INVOICE_STATUS_EXPIRED, self::SALES_INVOICE_STATUS_PROCESS, self::SALES_INVOICE_STATUS_PAID, self::SALES_INVOICE_STATUS_APPROVE], true);
    }
}
