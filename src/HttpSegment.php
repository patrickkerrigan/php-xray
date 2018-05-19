<?php

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 13/05/2018
 */
class HttpSegment extends RemoteSegment
{
    use HttpTrait;

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['http'] = $this->serialiseHttpData();

        return array_filter($data);
    }
}
