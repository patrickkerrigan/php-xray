<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
class SegmentTest extends TestCase
{
    public function testSegmentWithoutErrorsSerialisesCorrectly()
    {
        $segment = new Segment();

        $segment->setName('Test segment')
            ->setParentId('123')
            ->begin()
            ->end();

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertEquals('Test segment', $serialised['name']);
        $this->assertNotNull($serialised['start_time']);
        $this->assertNotNull($serialised['end_time']);
        $this->assertArrayNotHasKey('fault', $serialised);
        $this->assertArrayNotHasKey('error', $serialised);
        $this->assertArrayNotHasKey('subsegments', $serialised);
    }

    public function testSegmentWithErrorSerialisesCorrectly()
    {
        $segment = new Segment();

        $segment->setName('Test segment')
            ->setParentId('123')
            ->begin()
            ->end()
            ->setError(true);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertEquals('Test segment', $serialised['name']);
        $this->assertTrue($serialised['error']);
        $this->assertNotNull($serialised['start_time']);
        $this->assertNotNull($serialised['end_time']);
        $this->assertArrayNotHasKey('fault', $serialised);
        $this->assertArrayNotHasKey('subsegments', $serialised);
    }

    public function testSegmentWithFaultSerialisesCorrectly()
    {
        $segment = new Segment();

        $segment->setName('Test segment')
            ->setParentId('123')
            ->begin()
            ->end()
            ->setFault(true);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertEquals('Test segment', $serialised['name']);
        $this->assertTrue($serialised['fault']);
        $this->assertNotNull($serialised['start_time']);
        $this->assertNotNull($serialised['end_time']);
        $this->assertArrayNotHasKey('error', $serialised);
        $this->assertArrayNotHasKey('subsegments', $serialised);
    }

    public function testSegmentWithSubsegmentSerialisesCorrectly()
    {
        $segment = new Segment();
        $subsegment = new Segment();

        $subsegment->setName('Test subsegment')
            ->begin()
            ->end();

        $segment->setName('Test segment')
            ->setParentId('123')
            ->begin()
            ->addSubsegment($subsegment)
            ->end();

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertEquals('Test segment', $serialised['name']);
        $this->assertNotNull($serialised['start_time']);
        $this->assertNotNull($serialised['end_time']);
        $this->assertArrayHasKey('subsegments', $serialised);

        $this->assertEquals($subsegment, $serialised['subsegments'][0]);
    }

    public function testIsNotOpenIfEndTimeSet()
    {
        $segment = new Segment();
        $segment->begin()
            ->end();

        $this->assertFalse($segment->isOpen());
    }

    public function testIsOpenIfEndTimeNotSet()
    {
        $segment = new Segment();
        $segment->begin();

        $this->assertTrue($segment->isOpen());
    }

    public function testSubmitsIfSampled()
    {
        /** @var SegmentSubmitter|MockObject $submitter */
        $submitter = $this->createMock(SegmentSubmitter::class);

        $segment = new Segment();

        $submitter->expects($this->once())
            ->method('submitSegment')
            ->with($segment);

        $segment->setSampled(true)
            ->submit($submitter);

    }

    public function testDoesNotSubmitIfNotSampled()
    {
        /** @var SegmentSubmitter|MockObject $submitter */
        $submitter = $this->createMock(SegmentSubmitter::class);

        $segment = new Segment();

        $submitter->expects($this->never())
            ->method('submitSegment');

        $segment->setSampled(false)
            ->submit($submitter);

    }
}
