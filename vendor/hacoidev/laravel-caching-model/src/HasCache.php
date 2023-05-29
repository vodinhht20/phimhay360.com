<?php

namespace Hacoidev\CachingModel;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use \Hacoidev\CachingModel\Contracts\BuilderInterface;
use Hacoidev\CachingModel\Contracts\Cacheable;

trait HasCache
{
    protected static function bootHasCache()
    {
        static::updating(function ($instance) {
            static::flushRelationship($instance);
        });

        static::deleting(function ($instance) {
            static::flushRelationship($instance);
        });

        static::created(function ($instance) {
            Cache::forget(static::getCacheKeyList());
            static::flushRelationship($instance);
        });

        static::updated(function ($instance) {
            Cache::forget(static::getCacheKey($instance->{static::primaryCacheKey()}));
            static::flushRelationship($instance);
        });

        static::deleted(function ($instance) {
            Cache::forget(static::getCacheKey($instance->{static::primaryCacheKey()}));
            Cache::forget(static::getCacheKeyList());
            static::flushRelationship($instance);
        });

        if (method_exists(statis::class, 'trashed')) {
            static::restored(function ($instance) {
                Cache::forget(static::getCacheKey($instance->{static::primaryCacheKey()}));
                static::flushRelationship($instance);
            });
        }
    }

    public static function primaryCacheKey(): string
    {
        return 'id';
    }

    public static function getCacheKey($id, string $key = null): string
    {
        if (is_null($key)) {
            $key = static::primaryCacheKey();
        }

        return md5(sprintf("%s%s_%s_", Str::slug(__CLASS__), $key, $id));
    }

    public static function getCacheKeyList(): string
    {
        return md5(sprintf('all_%s_cached_keys', Str::slug(__CLASS__) . '.'));
    }

    public static function cacheTimeout(): int
    {
        return (int) config('cache.ttl.id', 24 * 3600);
    }

    public function scopeCacheWithRelation($query)
    {
        return $query;
    }

    final public static function fromCache(): BuilderInterface
    {
        return new CacheQueryBuilder(static::class);
    }

    protected static function flushRelationship($new)
    {
        $origin = static::getOrigin($new);
        foreach (($new->getTouchedRelations()) as $relation) {
            $newRelation = $new->{$relation};
            $oldRelation = $origin->{$relation};
            if ($newRelation instanceof Cacheable) {
                Cache::forget($newRelation->getCacheKey($newRelation->{$newRelation->primaryCacheKey()}));
            }
            if ($oldRelation instanceof Cacheable) {
                Cache::forget($oldRelation->getCacheKey($oldRelation->{$oldRelation->primaryCacheKey()}));
            }
        }
    }

    public static function getOrigin($instance)
    {
        $origin = new static;
        foreach ($instance->getOriginal() as $k => $v) {
            $origin->{$k} = $v;
        }

        return $origin;
    }
}
