<?php
namespace Pkerrigan\Xray\SamplingRule;

use Aws\Exception\AwsException;
use Aws\XRay\XRayClient;

/**
 * Retrives sampling rules from the AWS console
 *
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 30/06/2019
 */
class AwsSdkSamplingRuleRepository implements SamplingRuleRepository
{

    /** @var XRayClient */
    private $xrayClient;
    
    /** @var array|null */
    private $fallbackSamplingRule;

    public function __construct(
        XRayClient $xrayClient,
        array $fallbackSamplingRule = null
    )
    {
        $this->xrayClient = $xrayClient;
        $this->fallbackSamplingRule = $fallbackSamplingRule;
    }

    public function getAll(): array
    {
        try {
            $samplingRules = [];
            
            // See: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-xray-2016-04-12.html#getsamplingrules
            $samplingRulesResults = $this->xrayClient->getPaginator("GetSamplingRules");
            
            foreach ($samplingRulesResults as $samplingRuleResult) {
                foreach ($samplingRuleResult["SamplingRuleRecords"] as $samplingRule) {
                    $samplingRules[] = $samplingRule["SamplingRule"];
                }
            }
            
            return $samplingRules;
        } catch (AwsException $ex) {
            if (! empty($this->fallbackSamplingRule)) {
                return [ $this->fallbackSamplingRule ];
            }
            
            throw $ex;
        }
    }
}

