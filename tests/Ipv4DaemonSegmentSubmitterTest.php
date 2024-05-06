<?php

namespace Pkerrigan\Xray;

use Pkerrigan\Xray\Submission\DaemonSegmentSubmitter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 04/05/2024
 */
class Ipv4DaemonSegmentSubmitterTest extends AbstractDaemonSegmentSubmitterTest
{

    protected function getServerAddress(): string
    {
        return 'udp://127.0.0.1:2000';
    }

    protected function getSubmitter(): DaemonSegmentSubmitter
    {
        return new DaemonSegmentSubmitter(
            '127.0.0.1',
            2000
        );
    }
}