<?php

namespace Pkerrigan\Xray;

use JsonSerializable;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class Segment implements JsonSerializable
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string|null
     */
    protected $name;
    /**
     * @var float
     */
    protected $startTime;
    /**
     * @var float
     */
    protected $endTime;
    /**
     * @var Segment[]
     */
    protected $subsegments;

    public function __construct()
    {
        $this->id = bin2hex(random_bytes(8));
    }

    /**
     * @return static
     */
    public function begin()
    {
        $this->startTime = microtime(true);

        return $this;
    }

    /**
     * @return static
     */
    public function end()
    {
        $this->endTime = microtime(true);

        return $this;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param Segment $subsegment
     * @return static
     */
    public function addSubsegment(Segment $subsegment)
    {
        $this->subsegments[] = $subsegment;

        return $this;
    }

    /**
     * @param SegmentSubmitter $submitter
     */
    public function submit(SegmentSubmitter $submitter)
    {
        $submitter->submitSegment($this);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name ?? null,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'subsegments' => empty($this->subsegments) ? null : $this->subsegments
        ]);
    }
}
