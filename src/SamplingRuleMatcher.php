<?php
namespace Pkerrigan\Xray;

/**
 *
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 01/07/2019
 * @see https://docs.aws.amazon.com/xray/latest/devguide/xray-console-sampling.html
 */
class SamplingRuleMatcher
{
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
        
        $criterias = [
            $samplingRule["ServiceName"] => $trace->getName() ?? '',
            $samplingRule["ServiceType"] => $trace->getType() ?? '',
            $samplingRule["HTTPMethod"] => $trace->getMethod(),
            $samplingRule["URLPath"] => $url['path'] ?? '',
            $samplingRule["Host"] => $url['host'] ?? ''
        ];

        foreach ($criterias as $criteria => $input) {
            if (! Utils::matchesCriteria($criteria, $input)) {
                return false;
            }
        }
        
        return true;
    }
}

