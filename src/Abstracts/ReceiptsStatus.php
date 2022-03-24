<?php

namespace RLWebdiensten\LaravelInformerOnline\Abstracts;

class ReceiptsStatus
{
    const SALES_INVOICE_STATUS_OPEN = 'open';
    const SALES_INVOICE_STATUS_RECESSED = 'rocessed';

    public static function in_array($status): bool
    {
        return in_array($status, [self::SALES_INVOICE_STATUS_OPEN, self::SALES_INVOICE_STATUS_RECESSED], true);
    }

}