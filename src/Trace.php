<?php

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class Trace extends HttpSegment
{
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
     * @param string $traceId
     * @return static
     */
    public function setTraceId(string $traceId)
    {
        $this->traceId = $traceId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function begin()
    {
        parent::begin();

        if (is_null($this->traceId)) {
            $this->generateTraceId();
        }

        return $this;
    }

    public function end(TraceSubmitter $submitter = null)
    {
        parent::end();

        $submitter = $submitter ?? new DaemonTraceSubmitter();
        $submitter->submitTrace($this);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['trace_id'] = $this->traceId;

        return array_filter($data);
    }

    private function generateTraceId()
    {
        $startHex = dechex((int)$this->startTime);
        $uuid = bin2hex(random_bytes(12));

        $this->traceId = "1-{$startHex}-{$uuid}";
    }
}
