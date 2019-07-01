<?php
namespace Pkerrigan\Xray;

/**
 *
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 01/07/2019
 */
class SamplingRuleMatcher
{

    /**
     * Find the first sampling rule that matches the trace
     * 
     * @param Trace $trace
     * @param array $samplingRules
     * @return array|null
     */
    final public function matchFirst(Trace $trace, array $samplingRules)
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

