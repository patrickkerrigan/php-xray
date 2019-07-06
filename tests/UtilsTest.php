<?php
namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{    
    /**
     * 
     * @dataProvider provideSortSamplingRulesByPriority
     */
    public function testSortSamplingRulesByPriority($samplingRules, $expected)
    {
        $this->assertEquals($expected, Utils::sortSamplingRulesByPriorityDescending($samplingRules));
    }
    
    public function provideSortSamplingRulesByPriority()
    {
        return [
            "Sort by priority descending" => [
                [
                    [
                        "Priority" => 1000,
                        "RuleName" => "Default"
                    ],
                    [
                        "Priority" => 1,
                        "RuleName" => "Important"                   
                    ]
                ],
                [
                    [
                        "Priority" => 1,
                        "RuleName" => "Important"
                    ],
                    [
                        "Priority" => 1000,
                        "RuleName" => "Default"
                    ]
                ]
            ]
        ];
    }
}

