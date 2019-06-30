<?php
namespace Pkerrigan\Xray\SamplingRule;

use Psr\SimpleCache\CacheInterface;

/**
 * Proxy class used to cache retrieval of sampling rules
 * 
 * @author Niklas Ekman <nikl.ekman@gmail.com>
 * @since 30/06/2019
 */
class CachedSamplingRuleRepository implements SamplingRuleRepository
{

    private const CACHE_KEY = "Pkerrigan\\Xray\\SamplingRule";

    /** @var SamplingRuleRepository */
    private $samplingRuleRepository;

    /** @var CacheInterface */
    private $cache;

    /** @var int */
    private $cacheTtlSeconds;

    public function __construct(SamplingRuleRepository $samplingRuleRepository, CacheInterface $cache, int $cacheTtlSeconds = 3600)
    {
        $this->samplingRuleRepository = $samplingRuleRepository;
        $this->cache = $cache;
        $this->cacheTtlSeconds = $cacheTtlSeconds;
    }

    public function getAll(array $filters = []): array
    {
        $cacheKey = self::CACHE_KEY . "\\" . implode("-", $filters);
        
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $samplingRules = $this->samplingRuleRepository->getAll();
        $this->cache->set($cacheKey, $samplingRules, $this->cacheTtlSeconds);

        return $samplingRules;
    }
}

