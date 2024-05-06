<?php

namespace Pkerrigan\Xray;

use function array_filter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 14/05/2018
 */
class RemoteSegment extends Segment
{
    /**
     * @var bool
     */
    protected $traced = false;

    /**
     * @deprecated Please use HttpSegment.setTraced
     *
     * @param bool $traced
     * @return static
     */
    public function setTraced(bool $traced)
    {
        $this->traced = $traced;

        return $this;
    }

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['namespace'] = 'remote';
        $data['traced'] = $this->traced;

        return array_filter($data);
    }
}
