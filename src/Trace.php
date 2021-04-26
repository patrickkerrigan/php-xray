<?php

declare(strict_types=1);

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class Trace extends Segment
{
    use HttpTrait;

    private static $instance;
    private ?string $serviceVersion = null;
    private ?string $user = null;

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function setTraceHeader(string $traceHeader = null): self
    {
        if (is_null($traceHeader)) {
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
        $this->setSampled((bool) ($variables['Sampled'] ?? false));
        $this->setParentId($variables['Parent'] ?? null);

        return $this;
    }

    public function setServiceVersion(string $serviceVersion): self
    {
        $this->serviceVersion = $serviceVersion;

        return $this;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setClientIpAddress(string $clientIpAddress): self
    {
        $this->clientIpAddress = $clientIpAddress;

        return $this;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function begin(int $samplePercentage = 10): self
    {
        parent::begin();

        if (is_null($this->traceId)) {
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
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        $data['http'] = $this->serialiseHttpData();
        $data['service'] = empty($this->serviceVersion) ? null : ['version' => $this->serviceVersion];
        $data['user'] = $this->user;

        return array_filter($data);
    }

    private function generateTraceId(): void
    {
        $startHex = dechex((int)$this->startTime);
        $uuid = bin2hex(random_bytes(12));

        $this->setTraceId("1-{$startHex}-{$uuid}");
    }
}
