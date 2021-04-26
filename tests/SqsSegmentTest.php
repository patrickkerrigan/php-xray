<?php

declare(strict_types=1);

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

class SqsSegmentTest extends TestCase
{
    public function testSerialisesCorrectly(): void
    {
        $segment = new SqsSegment();
        $segment
            ->setQueueUrl('http://')
            ->setAwsAccountId(123);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals(123, $serialised['aws']['account_id']);
        $this->assertEquals('http://', $serialised['aws']['queue_url']);
        $this->assertEquals('SendMessage', $serialised['aws']['operation']);
    }
}
