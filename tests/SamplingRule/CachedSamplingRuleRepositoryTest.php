<?php
namespace Pkerrigan\Xray\SamplingRule;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class CachedSamplingRuleRepositoryTest extends TestCase
{
    public function testGetAllWhenCacheExists()
    {
        $expected = [
            [ 'fake_sampling_rule' ]
        ];
        
        $repository = $this->createMock(SamplingRuleRepository::class);
        $repository->expects($this->never())
            ->method('getAll');
        
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $cache->expects($this->once())
            ->method('get')
            ->willReturn($expected);
        $cache->expects($this->never())
            ->method('set');
        
        $cachedRepository = new CachedSamplingRuleRepository($repository, $cache);
        $this->assertEquals($expected, $cachedRepository->getAll());
    }
    
    public function testGetAllWhenCacheNotExists()
    {
        $expected = [
            [ 'fake_sampling_rule' ]
        ];
        
        $repository = $this->createMock(SamplingRuleRepository::class);
        $repository->expects($this->once())
            ->method('getAll')
            ->willReturn($expected);
        
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('has')
            ->willReturn(false);
        $cache->expects($this->never())
            ->method('get');
        $cache->expects($this->once())
            ->method('set');
        
        $cachedRepository = new CachedSamplingRuleRepository($repository, $cache);
        $this->assertEquals($expected, $cachedRepository->getAll());
    }
}

