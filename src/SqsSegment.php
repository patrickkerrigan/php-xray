<?php

namespace Pkerrigan\Xray;

class SqsSegment extends RemoteSegment
{
    /**
     * @var string|null
     */
    protected $queueUrl;

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        $data['aws'] = $this->serialiseAwsData();
        $data['namespace'] = 'aws';

        return array_filter($data);
    }

    /**
     * @param string|null $queueUrl
     * @return static
     */
    public function setQueueUrl(string $queueUrl)
    {
        $this->queueUrl = $queueUrl;

        return $this;
    }

    protected function serialiseAwsData(): array
    {
        return [
            'operation' => 'SendMessage',
            'queue_url' => $this->queueUrl,
        ];
    }
}