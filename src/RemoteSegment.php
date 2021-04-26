<?php

declare(strict_types=1);

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 14/05/2018
 */
class RemoteSegment extends Segment
{
    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        $data['namespace'] = 'remote';

        return array_filter($data);
    }
}
