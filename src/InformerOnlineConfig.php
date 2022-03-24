<?php

namespace RLWebdiensten\LaravelInformeronline;

use \RLWebdiensten\LaravelInformeronline\Contracts\InformerOnlineConfig as Config;

class InformerOnlineConfig implements Config
{

    public function __construct(protected ?string $baseUri = null, protected ?string $apiKey = null, protected ?int $securityCode = null)
    {
    }

    public function getBaseUri(): ?string
    {
        return $this->baseUri;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function getSecurityCode(): ?int
    {
        return $this->securityCode;
    }
}