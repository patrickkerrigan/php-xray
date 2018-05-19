<?php

namespace Pkerrigan\Xray;

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
    protected $traceId;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
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
        if (is_null($traceHeader)) {
            return $this;
        }

        $parts = explode(';', $traceHeader);

        $variables = array_map(function ($str): array {
            return explode('=', $str);
        }, $parts);

        $variables = array_column($variables, 1, 0);

        $this->traceId = $variables['Root'] ?? null;
        $this->setSampled($variables['Sampled'] ?? false);
        $this->setParentId($variables['Parent'] ?? null);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function begin(int $samplePercentage = 10)
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
     * @return string
     */
    public function getTraceId(): string
    {
        return $this->traceId;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['trace_id'] = $this->traceId;
        $data['http'] = $this->serialiseHttpData();

        return array_filter($data);
    }

    private function generateTraceId()
    {
        $startHex = dechex((int)$this->startTime);
        $uuid = bin2hex(random_bytes(12));

        $this->traceId = "1-{$startHex}-{$uuid}";
    }
}
