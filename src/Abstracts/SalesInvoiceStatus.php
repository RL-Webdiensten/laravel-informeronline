<?php

namespace RLWebdiensten\LaravelInformerOnline\Abstracts;

class SalesInvoiceStatus
{
    const SALES_INVOICE_STATUS_OPEN = 'open';
    const SALES_INVOICE_STATUS_EXPIRED = 'expired';
    const SALES_INVOICE_STATUS_COLLECT = 'collect';
    const SALES_INVOICE_STATUS_DRAFT = 'draft';

    public static function in_array($status): bool
    {
        return in_array($status, [self::SALES_INVOICE_STATUS_EXPIRED, self::SALES_INVOICE_STATUS_COLLECT, self::SALES_INVOICE_STATUS_DRAFT], true);
    }

}