<?php
namespace Pkerrigan\Xray;

class SamplingRuleMatcher
{

    final public function matchAny(Trace $trace, array $samplingRules): ?array
    {
        $samplingRules = Utils::sortSamplingRulesByPriorityDescending($samplingRules);

        foreach ($samplingRules as $samplingRule) {
            if ($this->match($trace, $samplingRule)) {
                return $samplingRule;
            }
        }

        return null;
    }

    public function match(Trace $trace, array $samplingRule): bool
    {
        $url = parse_url($trace->getUrl());

        return Utils::matchesCriteria($samplingRule["HTTPMethod"], $trace->getMethod())
            && Utils::matchesCriteria($samplingRule["URLPath"], $url['path'] ?? "")
            && Utils::matchesCriteria($samplingRule["Host"], $url['host'] ?? "");
    }
}

