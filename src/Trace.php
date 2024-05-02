<?php

namespace Pkerrigan\Xray;

use function explode;
use function array_map;
use function array_filter;
use function array_column;
use function random_int;
use function random_bytes;
use function dechex;
use function bin2hex;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class Trace extends Segment
{
    use HttpTrait;

    /**
     * @var static
     */
    private static $instance;
    /**
     * @var string
     */
    private $serviceVersion;
    /**
     * @var string
     */
    private $user;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $traceHeader
     * @return static
     */
    public function setTraceHeader(string $traceHeader = null)
    {
        if ($traceHeader === null) {
            return $this;
        }

        $parts = explode(';', $traceHeader);

        $variables = array_map(function ($str): array {
            return explode('=', $str);
        }, $parts);

        $variables = array_column($variables, 1, 0);

        if (isset($variables['Root'])) {
            $this->setTraceId($variables['Root']);
        }
        $this->setSampled($variables['Sampled'] ?? false);
        $this->setParentId($variables['Parent'] ?? null);

        return $this;
    }

    /**
     * @param string $serviceVersion
     * @return static
     */
    public function setServiceVersion(string $serviceVersion)
    {
        $this->serviceVersion = $serviceVersion;

        return $this;
    }

    /**
     * @param string $user
     * @return static
     */
    public function setUser(string $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $clientIpAddress
     * @return static
     */
    public function setClientIpAddress(string $clientIpAddress)
    {
        $this->clientIpAddress = $clientIpAddress;

        return $this;
    }

    /**
     * @param string $userAgent
     * @return static
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function begin(int $samplePercentage = 10)
    {
        parent::begin();

        if ($this->traceId === null) {
            $this->generateTraceId();
        }

        if (!$this->isSampled()) {
            $this->sampled = (random_int(0, 99) < $samplePercentage);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['http'] = $this->serialiseHttpData();
        $data['service'] = empty($this->serviceVersion) ? null : ['version' => $this->serviceVersion];
        $data['user'] = $this->user;

        return array_filter($data);
    }

    private function generateTraceId(): void
    {
        $startHex = dechex((int) $this->startTime);
        $uuid = bin2hex(random_bytes(12));

        $this->setTraceId("1-{$startHex}-{$uuid}");
    }
}
