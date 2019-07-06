<?php
namespace Pkerrigan\Xray\SamplingRule;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Xray\Trace;

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
                    ->setUrl('https://example.com/path')
                    ->setMethod('GET')
                    ->setName('application'),
                [
                    'HTTPMethod' => 'GET',
                    'Host' => 'example.com',
                    'URLPath' => '/path',
                    'ServiceName' => 'app*',
                    'ServiceType' => '*'
                ],
                true
            ]            
        ];
    }
    
    /** @dataProvider provideMatchFirst */
    public function testMatchFirst($trace, $samplingRules, $expected)
    {
        $this->assertEquals($expected, $this->samplingRuleMatcher->matchFirst($trace, $samplingRules));
    }
    
    public function provideMatchFirst()
    {
        return [
            [
                (new Trace())
                    ->setUrl('https://example.com/path')
                    ->setMethod('GET'),
                [
                    [
                        'Priority' => 1000,
                        'HTTPMethod' => 'GET',
                        'Host' => 'example.com',
                        'URLPath' => '/path',
                        'RuleName' => 'Default',
                        'ServiceName' => '*',
                        'ServiceType' => '*'
                    ],
                    [
                        'Priority' => 1,
                        'HTTPMethod' => 'GET',
                        'Host' => '*',
                        'URLPath' => '/any/path',
                        'RuleName' => 'Not matching',
                        'ServiceName' => '*',
                        'ServiceType' => '*'
                    ],
                    [
                        'Priority' => 5,
                        'HTTPMethod' => 'GET',
                        'Host' => '*',
                        'URLPath' => '/path',
                        'RuleName' => 'Important',
                        'ServiceName' => '*',
                        'ServiceType' => '*'
                    ]
                ],
                [
                    'Priority' => 5,
                    'HTTPMethod' => 'GET',
                    'Host' => '*',
                    'URLPath' => '/path',
                    'RuleName' => 'Important',
                    'ServiceName' => '*',
                    'ServiceType' => '*'
                ]
            ]
        ];
    }
    
    /**
     *
     * @dataProvider provideStringMatchesCriteria
     */
    public function testStringMatchesCriteria($criteria, $input, $expected)
    {
        $this->assertEquals($expected, $this->samplingRuleMatcher->stringMatchesCriteria($input, $criteria));
    }
    
    public function provideStringMatchesCriteria()
    {
        return [
            'Single-character wildcard (?)' => [
                'T?st',
                'Test',
                true
            ],
            'Single-character wildcard (?), too many characters' => [
                'T?st',
                'Testo',
                false
            ],
            'Single-character wildcard (?), too few characters' => [
                'T?st',
                'Tst',
                false
            ],
            'Multi-character wildcard (*), one character' => [
                'T*st',
                'Test',
                true
            ],
            'Multi-character wildcard (*), multiple characters' => [
                'T*st',
                'Teest',
                true
            ],
            'Multi-character wildcard (*)' => [
                'T*st',
                'Best',
                false
            ],
            'Multi-character wildcard (*), too few characters' => [
                'T*st',
                'Tst',
                true
            ],
            'One multi-character wildcard matches empty' => [
                '*',
                '',
                true
            ],
            'One wildcard character matches any single character' => [
                '?',
                'T',
                true
            ],
            'Case insensitivity' => [
                'test',
                'Test',
                true
            ],
            'Protect against arbitray regex' => [
                '(Test){2}',
                'TestTest',
                false
            ]
        ];
    }
}

