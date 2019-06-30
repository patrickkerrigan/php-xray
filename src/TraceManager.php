<?php
namespace Pkerrigan\Xray;

use Pkerrigan\Xray\SamplingRule\SamplingRuleRepository;
use Pkerrigan\Xray\Submission\SegmentSubmitter;
use Psr\Http\Message\ServerRequestInterface;

class TraceManager
{

    /** @var SamplingRuleRepository */
    private $samplingRuleRepository;

    /** @var RequestMatcher */
    private $requestMatcher;

    /** @var SegmentSubmitter */
    private $segmentSubmitter;

    public function __construct(
        SamplingRuleRepository $samplingRule,
        RequestMatcher $requestMatcher,
        SegmentSubmitter $segmentSubmitter)
    {
        $this->samplingRuleRepository = $samplingRule;
        $this->requestMatcher = $requestMatcher;
        $this->segmentSubmitter = $segmentSubmitter;
    }

    public function submit(
        ServerRequestInterface $request,
        Trace $trace): void
    {
        $samplingRules = $this->samplingRuleRepository->getAll();
        $samplingRule = $this->requestMatcher->matches($request, $samplingRules);
        $trace->setSampled($samplingRule !== null && (random_int(0, 99) < $samplingRule["FixedRate"] * 100));
        
        $trace->submit($this->segmentSubmitter);
    }
}

