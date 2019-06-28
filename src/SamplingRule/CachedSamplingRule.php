<?php
namespace Pkerrigan\Xray\SamplingRule;

use Psr\SimpleCache\CacheInterface;

class CachedSamplingRule implements SamplingRule
{

    public const CACHE_KEY = "Pkerrigan\Xray\SamplingRule";

    /** @var SamplingRule */
    private $samplingRule;

    /** @var CacheInterface */
    private $cache;

    /** @var int */
    private $cacheTtlSeconds;

    public function __construct(
        SamplingRule $samplingRule,
        CacheInterface $cache,
        int $cacheTtlSeconds = 3600)
    {
        $this->samplingRule = $samplingRule;
        $this->cache = $cache;
        $this->cacheTtlSeconds = $cacheTtlSeconds;
    }

    public function fetch(): array
    {
        if ($this->cache->has(self::CACHE_KEY)) {
            return $this->cache->get(self::CACHE_KEY);
        }

        $samplingRules = $this->samplingRule->fetch();
        $this->cache->set(self::CACHE_KEY, $samplingRules, $this->cacheTtlSeconds);

        return $samplingRules;
    }
}

