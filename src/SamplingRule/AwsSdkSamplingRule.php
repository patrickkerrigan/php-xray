<?php
namespace Pkerrigan\Xray\SamplingRule;

use Aws\XRay\XRayClient;

class AwsSdkSamplingRule implements SamplingRule
{

    /** @var XRayClient */
    private $xrayClient;

    public function __construct(
        XRayClient $xrayClient)
    {
        $this->xrayClient = $xrayClient;
    }

    public function fetch(): array
    {
        $samplingRules = [];
    
        // See: https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-xray-2016-04-12.html#getsamplingrules
        foreach ($this->xrayClient->getPaginator("getSamplingRules") as $samplingRule) {
            $samplingRules[] = $samplingRule["SamplingRule"];
        }

        return $samplingRules;
    }
}

