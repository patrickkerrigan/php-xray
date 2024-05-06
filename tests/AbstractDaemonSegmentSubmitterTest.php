<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Xray\Submission\DaemonSegmentSubmitter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
abstract class AbstractDaemonSegmentSubmitterTest extends TestCase
{
    /**
     * @var resource
     */
    private $socket;

    abstract protected function getServerAddress(): string;
    abstract protected function getSubmitter(): DaemonSegmentSubmitter;

    public function setUp(): void
    {
        parent::setUp();
        $_ = null;
        $this->socket = stream_socket_server(
            $this->getServerAddress(),
            $_,
            $_,
            STREAM_SERVER_BIND
        );
    }

    public function tearDown(): void
    {
        fclose($this->socket);
        parent::tearDown();
    }

    public function testSubmitsToDaemon(): void
    {
        $segment = new Segment();
        $segment->setSampled(true)
            ->setName('Test segment / 1')
            ->begin()
            ->end()
            ->submit($this->getSubmitter());

        $packets = $this->receivePackets(1);

        $this->assertPacketsReceived([$segment], $packets);
    }

    public function testSubmitsLongTraceAsFragmented(): void
    {
        $subsegment1 = (new SqlSegment())
            ->setQuery(str_repeat('a', 30000));
        $subsegment2 = (new SqlSegment())
            ->setQuery(str_repeat('b', 30000));
        $subsegment3 = (new SqlSegment())
            ->setQuery(str_repeat('c', 30000));

        $segment = new Trace();
        $segment->setSampled(true)
                ->setName('Test segment')
                ->begin()
                ->addSubsegment($subsegment1)
                ->addSubsegment($subsegment2)
                ->addSubsegment($subsegment3)
                ->end()
                ->submit($this->getSubmitter());

        $buffer = $this->receivePackets(5);

        $rawSegment = $segment->jsonSerialize();
        unset($rawSegment['subsegments']);
        $openingSegment = $rawSegment;
        unset($openingSegment['end_time']);
        $openingSegment['in_progress'] = true;

        $subsegment1->setIndependent(true)
                    ->setTraceId($segment->getTraceId())
                    ->setParentId($segment->getId());

        $subsegment2->setIndependent(true)
                    ->setTraceId($segment->getTraceId())
                    ->setParentId($segment->getId());

        $subsegment3->setIndependent(true)
                    ->setTraceId($segment->getTraceId())
                    ->setParentId($segment->getId());

        $expectedPackets = [$openingSegment, $subsegment1, $subsegment2, $subsegment3, $rawSegment];

        $this->assertPacketsReceived($expectedPackets, $buffer);
    }

    /**
     * @param $expectedPackets
     * @param $buffer
     */
    private function assertPacketsReceived($expectedPackets, $buffer): void
    {
        for ($i = 0, $iMax = count($expectedPackets); $i < $iMax; $i++) {
            $this->assertEquals(
                json_encode(DaemonSegmentSubmitter::HEADER)
                . "\n" .
                json_encode($expectedPackets[$i], JSON_UNESCAPED_SLASHES),
                $buffer[$i]
            );
        }
    }

    /**
     * @param int $number
     * @return array
     */
    private function receivePackets(int $number): array
    {
        $buffer = array_fill(0, $number, '');

        for ($i = 0; $i < $number; $i++) {
            $buffer[$i] = stream_socket_recvfrom($this->socket, 65535);
        }

        return $buffer;
    }
}
