<?php
namespace Pkerrigan\Xray;

use Pkerrigan\Xray\SamplingRule\SamplingRuleRepository;
use Pkerrigan\Xray\Submission\SegmentSubmitter;

class TraceManager
{

    /** @var SamplingRuleRepository */
    private $samplingRuleRepository;

    /** @var SamplingRuleMatcher */
    private $samplingRuleMatcher;

    /** @var SegmentSubmitter */
    private $segmentSubmitter;

    public function __construct(
        SamplingRuleRepository $samplingRuleRepository, 
        SegmentSubmitter $segmentSubmitter,
        ?SamplingRuleMatcher $samplingRuleMatcher = null
    )
    {
        $this->samplingRuleRepository = $samplingRuleRepository;
        $this->segmentSubmitter = $segmentSubmitter;
        $this->samplingRuleMatcher = $samplingRuleMatcher ?? new SamplingRuleMatcher();
    }

    public function submit(Trace $trace): void
    {
        $samplingRules = $this->samplingRuleRepository->getAll([
            "serviceName" => $trace->getName(),
            "serviceType" => $trace->getType()
        ]);
        
        $samplingRule = $this->samplingRuleMatcher->matchAny($trace, $samplingRules);
        $trace->setSampled($samplingRule !== null && (random_int(0, 99) < $samplingRule["FixedRate"] * 100));

        $trace->submit($this->segmentSubmitter);
    }
}

