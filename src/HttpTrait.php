<?php

declare(strict_types=1);

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 14/05/2018
 */
trait HttpTrait
{
    protected ?string $url = null;

    protected ?string $method = null;

    protected ?string $clientIpAddress = null;

    protected ?string $userAgent = null;

    protected ?int $responseCode = null;

    protected bool $traced = false;

    protected ?int $contentLength = null;

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function setContentLength(int $contentLength): self
    {
        $this->contentLength = $contentLength;

        return $this;
    }

    protected function serialiseHttpData(): array
    {
        return [
            'request' => array_filter([
                'url' => $this->url,
                'method' => $this->method,
                'client_ip' => $this->clientIpAddress,
                'user_agent' => $this->userAgent,
                'traced' => $this->traced
            ]),
            'response' => array_filter([
                'status' => $this->responseCode,
                'content_length' => $this->contentLength
            ])
        ];
    }
}
