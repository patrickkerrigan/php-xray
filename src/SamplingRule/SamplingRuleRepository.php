<?php
namespace Pkerrigan\Xray\SamplingRule;

/**
 * Responsible for retrieving sampling rules
 *
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 30/06/2019
 * @see https://docs.aws.amazon.com/xray/latest/devguide/xray-api-sampling.html
 */
interface SamplingRuleRepository
{
    
    /**
     * Get all sampling rules from the repository. There are two filters that should
     * be supported: serviceName and serviceType. They are used in order to retrieve
     * the correct sampling rules based on which application that Xray is running on.
     * 
     * @param array $filters
     * @return array
     */
    public function getAll(array $filters = []): array;
}

