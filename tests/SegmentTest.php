<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pkerrigan\Xray\Submission\SegmentSubmitter;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
class SegmentTest extends TestCase
{
    public function testSegmentWithoutErrorsSerialisesCorrectly(): void
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

    public function testSegmentWithErrorSerialisesCorrectly(): void
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

    public function testSegmentWithFaultSerialisesCorrectly(): void
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

    public function testSegmentWithSubsegmentSerialisesCorrectly(): void
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

    public function testIndependentSubsegmentSerialisesCorrectly(): void
    {
        $segment = new Segment();

        $segment->setName('Test segment')
                ->setParentId('123')
                ->setTraceId('456')
                ->setIndependent(true)
                ->begin()
                ->end();

        $serialised = $segment->jsonSerialize();

        $this->assertEquals('123', $serialised['parent_id']);
        $this->assertEquals('456', $serialised['trace_id']);
        $this->assertEquals('subsegment', $serialised['type']);
    }

    public function testGivenAnnotationsSerialisesCorrectly(): void
    {
        $segment = new Segment();
        $segment->addAnnotation('key1', 'value1')
            ->addAnnotation('key2', 'value2');

        $serialised = $segment->jsonSerialize();

        $this->assertEquals(
            [
                'key1' => 'value1',
                'key2' => 'value2'
            ],
            $serialised['annotations']
        );
    }

    public function testGivenMetadataSerialisesCorrectly(): void
    {
        $segment = new Segment();
        $segment->addMetadata('key1', 'value1')
            ->addMetadata('key2', ['value2', 'value3']);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals(
            [
                'key1' => 'value1',
                'key2' => ['value2', 'value3']
            ],
            $serialised['metadata']
        );
    }

    public function testAddingSubsegmentToClosedSegmentFails(): void
    {
        $segment = new Segment();
        $subsegment = new Segment();

        $subsegment->setName('Test subsegment')
            ->begin()
            ->end();

        $segment->setName('Test segment')
            ->setParentId('123')
            ->begin()
            ->end()
            ->addSubsegment($subsegment);

        $serialised = $segment->jsonSerialize();

        $this->assertArrayNotHasKey('subsegments', $serialised);
    }

    public function testAddingSubsegmentSetsSampled(): void
    {
        $segment = new Segment();
        $subsegment = new Segment();

        $subsegment->setName('Test subsegment')
            ->begin()
            ->end();

        $segment->setName('Test segment')
            ->setParentId('123')
            ->setSampled(true)
            ->begin()
            ->addSubsegment($subsegment)
            ->end();

        $this->assertTrue($subsegment->isSampled());
    }

    public function testIsNotOpenIfEndTimeSet(): void
    {
        $segment = new Segment();
        $segment->begin()
            ->end();

        $this->assertFalse($segment->isOpen());
    }

    public function testIsOpenIfEndTimeNotSet(): void
    {
        $segment = new Segment();
        $segment->begin();

        $this->assertTrue($segment->isOpen());
    }

    public function testSubmitsIfSampled(): void
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

    public function testDoesNotSubmitIfNotSampled(): void
    {
        /** @var SegmentSubmitter|MockObject $submitter */
        $submitter = $this->createMock(SegmentSubmitter::class);

        $segment = new Segment();

        $submitter->expects($this->never())
            ->method('submitSegment');

        $segment->setSampled(false)
            ->submit($submitter);

    }

    public function testGivenNoSubsegmentsCurrentSegmentReturnsSegment(): void
    {
        $segment = new Segment();
        $segment->begin();

        $this->assertEquals($segment, $segment->getCurrentSegment());
    }

    public function testClosedSubsegmentCurrentSegmentReturnsSegment(): void
    {
        $subsegment = new Segment();
        $subsegment->begin()
            ->end();
        $segment = new Segment();
        $segment->begin()
            ->addSubsegment($subsegment);

        $this->assertEquals($segment, $segment->getCurrentSegment());
    }

    public function testOpenSubsegmentCurrentSegmentReturnsSubsegment(): void
    {
        $subsegment = new Segment();
        $subsegment->begin();
        $segment = new Segment();
        $segment->begin()
            ->addSubsegment($subsegment);

        $this->assertEquals($subsegment, $segment->getCurrentSegment());
    }

    public function testSubsequentCallsCurrentSegmentReturnsSubsegment(): void
    {
        $subsegment = new Segment();
        $subsegment->begin();
        $segment = new Segment();
        $segment->begin()
                ->addSubsegment($subsegment);

        $this->assertEquals($subsegment, $segment->getCurrentSegment());
        $this->assertEquals($subsegment, $segment->getCurrentSegment());
    }

    public function testChangingCurrentSegmentReturnsCorrectStatus(): void
    {
        $subsegment1 = new Segment();
        $subsegment1->begin();
        $subsegment2 = new Segment();
        $subsegment2->begin();
        $subsegment3 = new Segment();
        $subsegment3->begin();

        $segment = new Segment();
        $segment->begin()
                ->addSubsegment($subsegment1)
                ->addSubsegment($subsegment2)
                ->addSubsegment($subsegment3);

        $this->assertEquals($subsegment1, $segment->getCurrentSegment());

        $subsegment1->end();

        $this->assertEquals($subsegment2, $segment->getCurrentSegment());

        $subsegment2->end();

        $this->assertEquals($subsegment3, $segment->getCurrentSegment());
    }
}
