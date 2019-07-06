<?php
namespace Pkerrigan\Xray\SamplingRule;

use PHPUnit\Framework\TestCase;
use Aws\XRay\XRayClient;

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
}

