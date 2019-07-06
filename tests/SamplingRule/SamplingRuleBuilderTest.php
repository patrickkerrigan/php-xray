<?php
namespace Pkerrigan\Xray\SamplingRule;

use PHPUnit\Framework\TestCase;

class SamplingRuleBuilderTest extends TestCase
{

    public function testBuild()
    {
        $samplingRuleBuilder = (new SamplingRuleBuilder())
            ->setFixedRate(75)
            ->setHost('example.com')
            ->setServiceName('app.example.com')
            ->setServiceType('*')
            ->setUrlPath('/my/path');

        $expected = [
            'FixedRate' => 0.75,
            'HTTPMethod' => '*',
            'Host' => 'example.com',
            'Priority' => 1,
            'ReservoirSize' => 1,
            'ResourceARN' => '*',
            'RuleARN' => '*',
            'RuleName' => 'Pkerrigan\\Xray\\SamplingRule',
            'ServiceName' => 'app.example.com',
            'ServiceType' => '*',
            'URLPath' => '/my/path'
        ];
        
        $this->assertEquals($expected, $samplingRuleBuilder->build());
    }
    
    public function testBuildWithCopyConstructor()
    {
        $copySamplingRule = (new SamplingRuleBuilder())
            ->setHost('example.com')
            ->setServiceName('app.example.com')
            ->build();
       
        $samplingRuleBuilder = (new SamplingRuleBuilder($copySamplingRule))
            ->setUrlPath('/path');
       
        $expected = [
            'FixedRate' => 1.0,
            'HTTPMethod' => '*',
            'Host' => 'example.com',
            'Priority' => 1,
            'ReservoirSize' => 1,
            'ResourceARN' => '*',
            'RuleARN' => '*',
            'RuleName' => 'Pkerrigan\\Xray\\SamplingRule',
            'ServiceName' => 'app.example.com',
            'ServiceType' => '*',
            'URLPath' => '/path'
        ];
        
        $this->assertEquals($expected, $samplingRuleBuilder->build());
    }
}

