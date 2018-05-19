<?php

namespace Pkerrigan\Xray;

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
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['namespace'] = 'remote';
        $data['traced'] = $this->traced;

        return array_filter($data);
    }
}
