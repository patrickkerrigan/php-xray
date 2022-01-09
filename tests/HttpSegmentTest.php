<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
class HttpSegmentTest extends TestCase
{
    public function testSerialisesCorrectly(): void
    {
        $segment = new HttpSegment();
        $segment->setUrl('http://example.com/')
            ->setMethod('GET')
            ->setResponseCode(200)
            ->setAwsAccountId(123);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals('remote', $serialised['namespace']);
        $this->assertEquals('http://example.com/', $serialised['http']['request']['url']);
        $this->assertEquals('GET', $serialised['http']['request']['method']);
        $this->assertEquals(200, $serialised['http']['response']['status']);
        $this->assertEquals(123, $serialised['aws']['account_id']);
    }

    public function testTracedSegmentSerialisesCorrectly(): void
    {
        $segment = new HttpSegment();
        $segment->setTraced(true);

        $serialised = $segment->jsonSerialize();

        $this->assertEquals($segment->getId(), $serialised['id']);
        $this->assertTrue($serialised['http']['request']['traced']);
    }
}
