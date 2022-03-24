<?php

namespace RLWebdiensten\LaravelInformeronline\Contracts;

interface InformerOnlineConfig
{
    public function getBaseUri(): ?string;

    public function getApiKey(): ?string;

    public function getSecurityCode(): ?int;
}
