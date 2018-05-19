<?php

namespace Pkerrigan\Xray\Submission;

use Pkerrigan\Xray\Segment;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class DaemonSegmentSubmitter implements SegmentSubmitter
{
    const HEADER = [
        'format' => 'json',
        'version' => 1
    ];

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    public function __construct(string $host = '127.0.0.1', int $port = 2000)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param Segment $segment
     */
    public function submitSegment(Segment $segment)
    {
        $packet = implode("\n", array_map('json_encode', [self::HEADER, $segment]));

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $packet, strlen($packet), 0, $this->host, $this->port);
        socket_close($socket);
    }
}
