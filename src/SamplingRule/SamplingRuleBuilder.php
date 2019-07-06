<?php
namespace Pkerrigan\Xray\SamplingRule;

class SamplingRuleBuilder
{
    /** @var array */
    private $samplingRule = [
        'FixedRate' => 1.0,
        'HTTPMethod' => '*',
        'Host' => '*',
        'Priority' => 1,
        'ReservoirSize' => 1,
        'ResourceARN' => '*',
        'RuleARN' => '*',
        'RuleName' => __NAMESPACE__,
        'ServiceName' => '*',
        'ServiceType' => '*',
        'URLPath' => '*'
    ];
    
    public function __construct(array $otherSamplingRule = [])
    {
        // Copy constructor
        foreach (array_keys($this->samplingRule) as $ruleKey) {
            if (isset($otherSamplingRule[$ruleKey])) {
                $this->samplingRule[$ruleKey] = $otherSamplingRule[$ruleKey];
            }
        }
    }
    
    public function setFixedRate(int $percentage): self
    {
        $this->samplingRule["FixedRate"] = $percentage / 100;
        
        return $this;
    }
    
    public function setHttpMethod(string $httpMethod): self
    {
        $this->samplingRule["HTTPMethod"] = $httpMethod;
        
        return $this;
    }
    
    public function setHost(string $host): self
    {
        $this->samplingRule["Host"] = $host;
        
        return $this;
    }
    
    public function setServiceName(string $serviceName): self
    {
        $this->samplingRule["ServiceName"] = $serviceName;
        
        return $this;
    }
    
    public function setServiceType(string $serviceType): self
    {
        $this->samplingRule["ServiceType"] = $serviceType;
        
        return $this;
    }
    
    public function setUrlPath(string $urlPath): self
    {
        $this->samplingRule["URLPath"] = $urlPath;
        
        return $this;
    }
    
    public function build(): array
    {
        return $this->samplingRule;
    }
}

