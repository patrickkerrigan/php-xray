<?php
namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;
use Pkerrigan\Xray\SamplingRule\SamplingRuleRepository;
use Pkerrigan\Xray\Submission\SegmentSubmitter;

class TraceServiceTest extends TestCase
{
    public function testSubmitTrace()
    {
        $samplingRule = ["FixedRate" => 0.25];
        
        $samplingRuleRepo = $this->createMock(SamplingRuleRepository::class);
        $samplingRuleRepo->expects($this->once())
            ->method("getAll")
            ->willReturn([ $samplingRule ]);
        
        $segmentSubmitter = $this->createMock(SegmentSubmitter::class);
        $segmentSubmitter->expects($this->atMost(1))
            ->method("submitSegment");
        
        $samplingRuleMatcher = $this->createMock(SamplingRuleMatcher::class);
        $samplingRuleMatcher->expects($this->once())
            ->method("matchFirst")
            ->willReturn($samplingRule);
        
        $traceService = new TraceService($samplingRuleRepo, $segmentSubmitter, $samplingRuleMatcher);
        
        $traceService->submitTrace(new Trace());
    }
}

