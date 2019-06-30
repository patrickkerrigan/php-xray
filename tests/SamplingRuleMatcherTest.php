<?php
namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

class SamplingRuleMatcherTest extends TestCase
{
    /** @var SamplingRuleMatcher */
    private $samplingRuleMatcher;
    
    protected function setUp()
    {
        parent::setUp();
        $this->samplingRuleMatcher = new SamplingRuleMatcher();
    }
    
    /** @dataProvider provideMatch */
    public function testMatch($trace, $samplingRule, $expected)
    {
        $this->assertEquals($expected, $this->samplingRuleMatcher->match($trace, $samplingRule));
    }
    
    public function provideMatch()
    {
        return [
            [
                (new Trace())
                    ->setUrl("https://example.com/path")
                    ->setMethod("GET"),
                [
                    "HTTPMethod" => "GET",
                    "Host" => "example.com",
                    "URLPath" => "/path"
                ],
                true
            ]            
        ];
    }
    
    /** @dataProvider provideMatchAny */
    public function testMatchAny($trace, $samplingRules, $expected)
    {
        $this->assertEquals($expected, $this->samplingRuleMatcher->matchAny($trace, $samplingRules));
    }
    
    public function provideMatchAny()
    {
        return [
            [
                (new Trace())
                    ->setUrl("https://example.com/path")
                    ->setMethod("GET"),
                [
                    [
                        "Priority" => 1000,
                        "HTTPMethod" => "GET",
                        "Host" => "example.com",
                        "URLPath" => "/path",
                        "RuleName" => "Default"
                    ],
                    [
                        "Priority" => 1,
                        "HTTPMethod" => "GET",
                        "Host" => "*",
                        "URLPath" => "/any/path",
                        "RuleName" => "Not matching"
                    ],
                    [
                        "Priority" => 5,
                        "HTTPMethod" => "GET",
                        "Host" => "*",
                        "URLPath" => "/path",
                        "RuleName" => "Important"
                    ]
                ],
                [
                    "Priority" => 5,
                    "HTTPMethod" => "GET",
                    "Host" => "*",
                    "URLPath" => "/path",
                    "RuleName" => "Important"
                ]
            ]
        ];
    }
}

