<?php
namespace Pkerrigan\Xray\SamplingRule;

use Pkerrigan\Xray\Trace;
use Pkerrigan\Xray\Utils;

/**
 *
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 01/07/2019
 * @see https://docs.aws.amazon.com/xray/latest/devguide/xray-console-sampling.html
 */
class SamplingRuleMatcher
{
    public function matchFirst(Trace $trace, array $samplingRules)
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
            $samplingRule['ServiceName'] => $trace->getName() ?? '',
            $samplingRule['ServiceType'] => $trace->getType() ?? '',
            $samplingRule['HTTPMethod'] => $trace->getMethod(),
            $samplingRule['URLPath'] => $url['path'] ?? '',
            $samplingRule['Host'] => $url['host'] ?? ''
        ];

        foreach ($criterias as $criteria => $input) {
            if (! $this->stringMatchesCriteria($input, $criteria)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function stringMatchesCriteria(string $input, string $criteria): bool
    {
        /*
         * Check if a criteria matches a given input. A criteria can include a multi-character wildcard (*)
         * or a single-character wildcard (?)
         * See: https://docs.aws.amazon.com/xray/latest/devguide/xray-console-sampling.html?icmpid=docs_xray_console#xray-console-sampling-options
         */
        if ($criteria === '*') {
            return true;
        }
        
        // Lets use regex in order to determine if the criteria matches. Quoting the criteria
        // will assure that the user can't enter any arbitray regex in the AWS console
        $criteria = str_replace(['\\*', '\\?'], ['.+', '.{1}'], preg_quote($criteria, '/'));
        
        return preg_match("/^{$criteria}$/i", $input) === 1;
    }
}

