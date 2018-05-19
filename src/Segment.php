<?php

namespace Pkerrigan\Xray;

use JsonSerializable;
use Pkerrigan\Xray\Submission\SegmentSubmitter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class Segment implements JsonSerializable
{
    /**
     * @var string
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected $id;
    /**
     * @var string
     */
    protected $parentId;
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
    protected $subsegments = [];
    /**
     * @var bool
     */
    protected $error = false;
    /**
     * @var bool
     */
    protected $fault = false;
    /**
     * @var bool
     */
    protected $sampled = false;

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
     * @param bool $error
     * @return static
     */
    public function setError(bool $error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @param bool $fault
     * @return static
     */
    public function setFault(bool $fault)
    {
        $this->fault = $fault;

        return $this;
    }

    /**
     * @param Segment $subsegment
     * @return static
     */
    public function addSubsegment(Segment $subsegment)
    {
        if (!$this->isOpen()) {
            return $this;
        }

        $this->subsegments[] = $subsegment;
        $subsegment->setSampled($this->isSampled());

        return $this;
    }

    /**
     * @param SegmentSubmitter $submitter
     */
    public function submit(SegmentSubmitter $submitter)
    {
        if (!$this->isSampled()) {
            return;
        }

        $submitter->submitSegment($this);
    }

    /**
     * @return bool
     */
    public function isSampled(): bool
    {
        return $this->sampled;
    }

    /**
     * @param bool $sampled
     * @return static
     */
    public function setSampled(bool $sampled)
    {
        $this->sampled = $sampled;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $parentId
     * @return static
     */
    public function setParentId(string $parentId = null)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return !is_null($this->startTime) && is_null($this->endTime);
    }

    /**
     * @return Segment
     */
    public function getCurrentSegment(): Segment
    {
        foreach ($this->subsegments as $subsegment) {
            if ($subsegment->isOpen()) {
                return $subsegment->getCurrentSegment();
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return array_filter([
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'name' => $this->name ?? null,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'subsegments' => empty($this->subsegments) ? null : $this->subsegments,
            'fault' => $this->fault,
            'error' => $this->error
        ]);
    }
}
