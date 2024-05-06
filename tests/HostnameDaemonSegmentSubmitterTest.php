<?php

namespace Pkerrigan\Xray;

use Pkerrigan\Xray\Submission\DaemonSegmentSubmitter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 04/05/2024
 */
class HostnameDaemonSegmentSubmitterTest extends AbstractDaemonSegmentSubmitterTest
{

    protected function getServerAddress(): string
    {
        return 'udp://[::1]:2000';
    }

    protected function getSubmitter(): DaemonSegmentSubmitter
    {
        return new DaemonSegmentSubmitter(
            'localhost',
            2000
        );
    }
}