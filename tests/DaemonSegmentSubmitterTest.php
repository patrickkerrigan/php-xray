<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Xray\Submission\DaemonSegmentSubmitter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
class DaemonSegmentSubmitterTest extends TestCase
{
    public function testSubmitsToDaemon()
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($socket, '127.0.0.1', 2000);

        $segment = new Segment();
        $segment->setSampled(true)
            ->setName('Test segment')
            ->begin()
            ->end()
            ->submit(new DaemonSegmentSubmitter());

        $from = '';
        $port = 0;
        socket_recvfrom($socket, $buffer, 512, 0, $from, $port);
        socket_close($socket);

        $this->assertEquals(
            json_encode(DaemonSegmentSubmitter::HEADER) . "\n" . json_encode($segment),
            $buffer
        );
    }
}
