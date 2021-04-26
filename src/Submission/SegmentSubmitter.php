<?php

declare(strict_types=1);

namespace Pkerrigan\Xray\Submission;

use Pkerrigan\Xray\Segment;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
interface SegmentSubmitter
{
    public function submitSegment(Segment $segment): void;
}
