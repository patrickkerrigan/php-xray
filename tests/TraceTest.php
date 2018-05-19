<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 19/05/2018
 */
class TraceTest extends TestCase
{
    public function testGetInstanceReturnsSingleton()
    {
        $instance1 = Trace::getInstance();
        $instance2 = Trace::getInstance();

        $this->assertEquals(spl_object_hash($instance1), spl_object_hash($instance2));
    }

    public function testSerialisesCorrectly()
    {
        $trace = new Trace();
        $trace->setName('Test trace')
            ->setUrl('http://example.com')
            ->setMethod('GET')
            ->setResponseCode(200)
            ->begin()
            ->end();

        $serialised = $trace->jsonSerialize();

        $this->assertEquals('Test trace', $serialised['name']);
        $this->assertEquals('http://example.com', $serialised['http']['request']['url']);
        $this->assertEquals('GET', $serialised['http']['request']['method']);
        $this->assertEquals(200, $serialised['http']['response']['status']);
        $this->assertEquals($trace->getTraceId(), $serialised['trace_id']);
    }

    public function testGeneratesCorrectFormatTraceId()
    {
        $trace = new Trace();
        $trace->begin();

        $this->assertRegExp('@^1\-[a-f0-9]{8}\-[a-f0-9]{24}$@', $trace->getTraceId());
    }

    /**
     * @expectedException \TypeError
     */
    public function testGivenNullHeaderDoesNotSetId()
    {
        $trace = new Trace();
        $trace->setTraceHeader(null);

        $trace->getTraceId();
    }

    public function testGivenIdHeaderSetsId()
    {
        $traceId = '1-ab3169f3-1b7f38ac63d9037ef1843ca4';

        $trace = new Trace();
        $trace->setTraceHeader("Root=$traceId");

        $this->assertEquals($traceId, $trace->getTraceId());
        $this->assertFalse($trace->isSampled());
        $this->assertArrayNotHasKey('parent_id', $trace->jsonSerialize());
    }

    public function testGivenSampledHeaderSetsSampled()
    {
        $traceId = '1-ab3169f3-1b7f38ac63d9037ef1843ca4';

        $trace = new Trace();
        $trace->setTraceHeader("Root=$traceId;Sampled=1");

        $this->assertEquals($traceId, $trace->getTraceId());
        $this->assertTrue($trace->isSampled());
        $this->assertArrayNotHasKey('parent_id', $trace->jsonSerialize());
    }

    public function testGivenParentHeaderSetsParentId()
    {
        $traceId = '1-ab3169f3-1b7f38ac63d9037ef1843ca4';
        $parentId = '1234567890';

        $trace = new Trace();
        $trace->setTraceHeader("Root=$traceId;Sampled=1;Parent=$parentId");

        $this->assertEquals($traceId, $trace->getTraceId());
        $this->assertTrue($trace->isSampled());
        $this->assertEquals($parentId, $trace->jsonSerialize()['parent_id']);
    }
}
