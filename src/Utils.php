<?php
namespace Pkerrigan\Xray;

/**
 * 
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/06/2019
 * @internal
 */
class Utils
{    
    public static function sortSamplingRulesByPriorityDescending(array $samplingRules): array
    {
        usort($samplingRules, function($samplingRule, $samplingRuleOther) {
            return $samplingRule['Priority'] - $samplingRuleOther['Priority'];
        });
        
        return $samplingRules;
    }
    
    public static function randomPossibility(int $percentage): bool
    {
        return random_int(0, 99) < $percentage;
    }
}

