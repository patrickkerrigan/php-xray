<?php

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class DaemonTraceSubmitter implements TraceSubmitter
{
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

    public function submitTrace(Trace $trace)
    {
        $header = [
            'format' => 'json',
            'version' => 1
        ];

        $packet = implode("\n", array_map('json_encode', [$header, $trace]));

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $packet, strlen($packet), 0, $this->host, $this->port);
        socket_close($socket);
    }
}
