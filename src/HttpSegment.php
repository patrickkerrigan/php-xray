<?php

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class HttpSegment extends Segment
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
     * @var int
     */
    protected $responseCode;

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

    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['http'] = [
            'request' => [
                'url' => $this->url,
                'method' => $this->method
            ] ,
            'response' => [
                'status' => $this->responseCode
            ]
        ];

        return array_filter($data);
    }
}
