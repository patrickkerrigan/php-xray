<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

class SqsSegmentTest extends TestCase
{
    public function testSerialisesCorrectly()
    {
        $segment = new SqsSegment();
        $segment->setQueueUrl('http://');

        $serialised = $segment->jsonSerialize();

        $this->assertEquals('http://', $serialised['aws']['queue_url']);
        $this->assertEquals('SendMessage', $serialised['aws']['operation']);
    }
}
