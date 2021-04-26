<?php

declare(strict_types=1);

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
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        $data['http'] = $this->serialiseHttpData();

        return array_filter($data);
    }

    public function setTraced(bool $traced): self
    {
        $this->traced = $traced;

        return $this;
    }
}
