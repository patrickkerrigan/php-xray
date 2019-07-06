<?php
namespace Pkerrigan\Xray\SamplingRule;

use PHPUnit\Framework\TestCase;
use Aws\XRay\XRayClient;
use Aws\Exception\AwsException;

class AwsSdkSamplingRuleRepositoryTest extends TestCase
{

    public function testGetAll()
    {
        $xrayClient = $this->createMock(XRayClient::class);
        $xrayClient->expects($this->once())
            ->method('getPaginator')
            ->with($this->equalTo('GetSamplingRules'))
            ->willReturn(new \ArrayIterator([
                [
                    'SamplingRuleRecords' => [
                        [
                            'SamplingRule' => [
                                'ServiceName' => '*',
                                'ServiceType' => '*'
                            ]
                        ]
                    ]
                ]
            ]));

        $repository = new AwsSdkSamplingRuleRepository($xrayClient);

        $expected = [
            [
                'ServiceName' => '*',
                'ServiceType' => '*'
            ]
        ];
        
        $this->assertEquals($expected, $repository->getAll());
    }
    
    public function testGetAllAwsErrorWithFallback()
    {
        $exception = $this->createMock(AwsException::class);
        
        $xrayClient = $this->createMock(XRayClient::class);
        $xrayClient->expects($this->once())
            ->method('getPaginator')
            ->with($this->equalTo('GetSamplingRules'))
            ->will($this->throwException($exception));
        
        $fallbackSamplingRule = [ 'my_fake_fallback_sampling_rule' ];
            
        $repository = new AwsSdkSamplingRuleRepository($xrayClient, $fallbackSamplingRule);
        
        $samplingRules = $repository->getAll();
        
        $this->assertCount(1, $samplingRules);
        $this->assertEquals($fallbackSamplingRule, $samplingRules[0]);
    }
    
    public function testGetAllAwsErrorWithoutFallback()
    {
        $exception = $this->createMock(AwsException::class);
        
        $this->expectException(get_class($exception));
        
        $xrayClient = $this->createMock(XRayClient::class);
        $xrayClient->expects($this->once())
            ->method('getPaginator')
            ->with($this->equalTo('GetSamplingRules'))
            ->will($this->throwException($exception));
        
        $repository = new AwsSdkSamplingRuleRepository($xrayClient);
        
        $repository->getAll();
    }
}

