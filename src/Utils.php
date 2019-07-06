<?php
namespace Pkerrigan\Xray;

/**
 * 
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 30/06/2019
 * @internal
 */
class Utils
{
    /**
     * Check if a criteria matches a given input. A criteria can include a multi-character wildcard (*)
     * or a single-character wildcard (?)
     * 
     * @param string $criteria
     * @param string $input
     * @return bool
     * @see https://docs.aws.amazon.com/xray/latest/devguide/xray-console-sampling.html?icmpid=docs_xray_console#xray-console-sampling-options
     */
    public static function matchesCriteria(string $criteria, string $input): bool
    {        
        if ($criteria === "*") {
            return true;
        }
        
        // Lets use regex in order to determine if the criteria matches. Quoting the criteria
        // will assure that the user can't enter any arbitray regex in the AWS console
        $criteria = str_replace(["\\*", "\\?"], [".+", ".{1}"], preg_quote($criteria, "/"));
        
        return preg_match("/^{$criteria}$/i", $input) === 1;
    }
    
    public static function sortSamplingRulesByPriorityDescending(array $samplingRules): array
    {
        usort($samplingRules, function($samplingRule, $samplingRuleOther) {
            return $samplingRule["Priority"] - $samplingRuleOther["Priority"];
        });
        
        return $samplingRules;
    }
    
    public static function randomPossibility(int $percentage): bool
    {
        return random_int(0, 99) < $percentage;
    }
}

