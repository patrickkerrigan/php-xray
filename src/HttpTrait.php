<?php

namespace Pkerrigan\Xray;

use function array_filter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 14/05/2018
 */
trait HttpTrait
{
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $clientIpAddress;
    /**
     * @var string
     */
    protected $userAgent;
    /**
     * @var int
     */
    protected $responseCode;
    /**
     * @var bool
     */
    protected $traced = false;

    /**
     * @param string $url
     * @return static
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $method
     * @return static
     */
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param int $responseCode
     * @return static
     */
    public function setResponseCode(int $responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * @return array
     */
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
                'status' => $this->responseCode
            ])
        ];
    }
}
