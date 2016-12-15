<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

use Doctrine\Common\Cache;

/**
 * Factory for doctrine caches
 */
class DoctrineCacheFactory
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * Mapping between cache class and the php extension it requires.
     *
     * @var array
     */
    protected static $knownCaches = [
        Cache\ApcuCache::class       => 'apc',
        Cache\MemcachedCache::class  => 'memcached',
        Cache\MemcacheCache::class   => 'memcache',
        Cache\ZendDataCache::class   => 'Zend Data Cache',
        Cache\SQLite3Cache::class    => 'sqlite3',
        Cache\RedisCache::class      => 'phpredis',
        Cache\XcacheCache::class     => 'xcache',
        Cache\WinCacheCache::class   => 'wincache',
        Cache\PhpFileCache::class    => null,
        Cache\FilesystemCache::class => null,
        Cache\ArrayCache::class      => null,
        //        Cache\MongoDBCache::class => '',
        //        Cache\PredisCache::class => '',
        //        Cache\CouchbaseCache::class => '',
        //        Cache\ChainCache::class => '',
        //        Cache\RiakCache::class => '',
    ];

    /**
     * @param string $namespace
     */
    public function __construct($namespace = __DIR__)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param string[]|null $priority
     *
     * @return Cache\Cache
     */
    public function __invoke(array $priority = null)
    {
        /** @var Cache\Cache $cacheInstance */
        $cacheInstance = null;
        foreach ($priority ?: array_keys(static::$knownCaches) as $cacheClass) {
            if (!array_key_exists($cacheClass, static::$knownCaches)) {
                continue;
            }
            $requiredExtension = static::$knownCaches[$cacheClass];
            if (!$requiredExtension || extension_loaded($requiredExtension)) {
                new $cacheClass();
                break;
            }
        }
        $cacheInstance = $cacheInstance ?: new Cache\ArrayCache();
        if ($cacheInstance instanceof Cache\CacheProvider) {
            $cacheInstance->setNamespace($this->namespace);
        }
        return $cacheInstance;
    }
}