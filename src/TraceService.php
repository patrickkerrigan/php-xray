<?php
namespace Pkerrigan\Xray;

use Pkerrigan\Xray\SamplingRule\SamplingRuleRepository;
use Pkerrigan\Xray\Submission\SegmentSubmitter;
use Pkerrigan\Xray\Submission\DaemonSegmentSubmitter;

/**
 * This layer sits ontop of the segment submitter to control which traces are submitted
 * 
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 01/07/2019
 */
class TraceService
{

    /** @var SamplingRuleRepository */
    private $samplingRuleRepository;
    
    /** @var SegmentSubmitter */
    private $segmentSubmitter;

    /** @var SamplingRuleMatcher */
    private $samplingRuleMatcher;

    public function __construct(
        SamplingRuleRepository $samplingRuleRepository, 
        ?SegmentSubmitter $segmentSubmitter = null,
        ?SamplingRuleMatcher $samplingRuleMatcher = null
    )
    {
        $this->samplingRuleRepository = $samplingRuleRepository;
        $this->segmentSubmitter = $segmentSubmitter ?? new DaemonSegmentSubmitter();
        $this->samplingRuleMatcher = $samplingRuleMatcher ?? new SamplingRuleMatcher();
    }

    public function submitTrace(Trace $trace)
    {
        $samplingRules = $this->samplingRuleRepository->getAll();
        $samplingRule = $this->samplingRuleMatcher->matchFirst($trace, $samplingRules);
        
        $isSampled = $samplingRule !== null && Utils::randomPossibility($samplingRule["FixedRate"] * 100);
        $trace->setSampled($isSampled);

        if ($isSampled) {
            $this->segmentSubmitter->submitSegment($trace);
        }
    }
}

