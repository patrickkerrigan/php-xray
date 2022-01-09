<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
class RemoteSegmentTest extends TestCase
{
    public function testUntracedSegmentSerialisesCorrectly(): void
    {
        $segment = new RemoteSegment();

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertEquals('remote', $serialised['namespace']);
        $this->assertArrayNotHasKey('traced', $serialised);
    }

    public function testTracedSegmentSerialisesCorrectly(): void
    {
        $segment = new RemoteSegment();
        $segment->setTraced(true);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertEquals('remote', $serialised['namespace']);
        $this->assertTrue($serialised['traced']);
    }
}
