<?php

namespace Pkerrigan\Xray\Submission;

use Pkerrigan\Xray\Segment;

use function stream_socket_client;
use function stream_set_write_buffer;
use function fclose;
use function fwrite;
use function strlen;
use function implode;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class DaemonSegmentSubmitter implements SegmentSubmitter
{
    public const MAX_SEGMENT_SIZE = 64000;

    public const HEADER = [
        'format' => 'json',
        'version' => 1
    ];

    /**
     * @var resource
     */
    private $socket;

    public function __construct(string $host = '127.0.0.1', int $port = 2000)
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            $host = "[$host]";
        }

        $this->socket = stream_socket_client("udp://$host:$port");

        if ($this->socket !== false) {
            stream_set_write_buffer($this->socket, 0);
        }
    }

    public function __destruct()
    {
        if ($this->socket === false) {
            return;
        }

        fclose($this->socket);
    }

    /**
     * @param Segment $segment
     * @return void
     */
    public function submitSegment(Segment $segment)
    {
        $packet = $this->buildPacket($segment);

        if (strlen($packet) > self::MAX_SEGMENT_SIZE) {
            $this->submitFragmented($segment);
            return;
        }

        $this->sendPacket($packet);
    }

    /**
     * @param Segment|array $segment
     * @return string
     */
    private function buildPacket($segment): string
    {
        return implode("\n", [
            json_encode(self::HEADER),
            json_encode($segment, JSON_UNESCAPED_SLASHES)
        ]);
    }

    /**
     * @param string $packet
     * @return void
     */
    private function sendPacket(string $packet): void
    {
        if ($this->socket === false) {
            return;
        }

        fwrite($this->socket, $packet);
    }

    /**
     * @param Segment $segment
     * @return void
     */
    private function submitFragmented(Segment $segment): void
    {
        $rawSegment = $segment->jsonSerialize();
        /** @var Segment[] $subsegments */
        $subsegments = $rawSegment['subsegments'] ?? [];
        unset($rawSegment['subsegments']);
        $this->submitOpenSegment($rawSegment);

        foreach ($subsegments as $subsegment) {
            $subsegment = clone $subsegment;
            $subsegment->setParentId($segment->getId())
                       ->setTraceId($segment->getTraceId())
                       ->setIndependent(true);
            $this->submitSegment($subsegment);
        }

        $completePacket = $this->buildPacket($rawSegment);
        $this->sendPacket($completePacket);
    }

    /**
     * @param array $openSegment
     * @return void
     */
    private function submitOpenSegment(array $openSegment): void
    {
        unset($openSegment['end_time']);
        $openSegment['in_progress'] = true;
        $initialPacket = $this->buildPacket($openSegment);
        $this->sendPacket($initialPacket);
    }
}
