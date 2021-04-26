<?php

declare(strict_types=1);

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
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected string $id;

    protected ?string $parentId = null;

    protected ?string $traceId = null;

    protected ?string $name;

    protected ?float $startTime = null;

    protected ?float $endTime = null;
    /**
     * @var Segment[]
     */
    protected array $subsegments = [];

    protected bool $error = false;

    protected bool $fault = false;

    protected bool $sampled = false;

    protected bool $independent = false;
    /**
     * @var string[]
     */
    private array $annotations;
    /**
     * @var string[]
     */
    private array $metadata;
    private int $lastOpenSegment = 0;
    private ?int $awsAccountId = null;

    public function __construct()
    {
        $this->id = bin2hex(random_bytes(8));
    }

    public function begin(): self
    {
        $this->startTime = microtime(true);

        return $this;
    }

    public function end(): self
    {
        $this->endTime = microtime(true);

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setError(bool $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function setFault(bool $fault): self
    {
        $this->fault = $fault;

        return $this;
    }

    public function addSubsegment(Segment $subsegment): self
    {
        if (!$this->isOpen()) {
            return $this;
        }

        $this->subsegments[] = $subsegment;
        $subsegment->setSampled($this->isSampled());

        return $this;
    }

    public function submit(SegmentSubmitter $submitter): void
    {
        if (!$this->isSampled()) {
            return;
        }

        $submitter->submitSegment($this);
    }

    public function isSampled(): bool
    {
        return $this->sampled;
    }

    public function setSampled(bool $sampled): self
    {
        $this->sampled = $sampled;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setParentId(string $parentId = null): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function setTraceId(string $traceId): self
    {
        $this->traceId = $traceId;

        return $this;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function isOpen(): bool
    {
        return !is_null($this->startTime) && is_null($this->endTime);
    }

    public function setIndependent(bool $independent): self
    {
        $this->independent = $independent;

        return $this;
    }

    public function setAwsAccountId(int $awsAccountId): self
    {
        $this->awsAccountId = $awsAccountId;

        return $this;
    }

    public function addAnnotation(string $key, string $value): self
    {
        $this->annotations[$key] = $value;

        return $this;
    }

    public function addMetadata(string $key, $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    public function getCurrentSegment(): Segment
    {
        for ($max = count($this->subsegments); $this->lastOpenSegment < $max; $this->lastOpenSegment++) {
            if ($this->subsegments[$this->lastOpenSegment]->isOpen()) {
                return $this->subsegments[$this->lastOpenSegment]->getCurrentSegment();
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'trace_id' => $this->traceId,
            'name' => $this->name ?? null,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'subsegments' => empty($this->subsegments) ? null : $this->subsegments,
            'type' => $this->independent ? 'subsegment' : null,
            'fault' => $this->fault,
            'error' => $this->error,
            'annotations' => empty($this->annotations) ? null : $this->annotations,
            'metadata' => empty($this->metadata) ? null : $this->metadata,
            'aws' => $this->serialiseAwsData(),
        ]);
    }

    protected function serialiseAwsData(): array
    {
        return [
            'account_id' => $this->awsAccountId,
        ];
    }
}
