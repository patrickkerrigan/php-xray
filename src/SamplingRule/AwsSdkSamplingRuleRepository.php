<?php
namespace Pkerrigan\Xray\SamplingRule;

use Aws\XRay\XRayClient;
use Pkerrigan\Xray\Utils;

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

    public function __construct(XRayClient $xrayClient)
    {
        $this->xrayClient = $xrayClient;
    }

    public function getAll(array $filters = []): array
    {
        $samplingRules = [];
        
        $serviceName = $filters["serviceName"] ?? "";
        $serviceType = $filters["serviceType"] ?? "";

        // See: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-xray-2016-04-12.html#getsamplingrules
        $samplingRulesResults = $this->xrayClient->getPaginator("GetSamplingRules");

        foreach ($samplingRulesResults as $samplingRuleResult) {
            foreach ($samplingRuleResult["SamplingRuleRecords"] as $samplingRule) {
                $samplingRule = $samplingRule["SamplingRule"];
                
                if (! Utils::matchesCriteria($samplingRule["ServiceName"], $serviceName)) {
                    continue;
                }
                
                if (! Utils::matchesCriteria($samplingRule["ServiceType"], $serviceType)) {
                    continue;
                }
                
                $samplingRules[] = $samplingRule;
            }
        }

        return $samplingRules;
    }
}

