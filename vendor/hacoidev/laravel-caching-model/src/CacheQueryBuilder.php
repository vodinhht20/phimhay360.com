<?php

namespace Hacoidev\CachingModel;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use \Hacoidev\CachingModel\Contracts\BuilderInterface;
use Closure;
use \Hacoidev\CachingModel\Contracts\Cacheable;
use \Hacoidev\CachingModel\Exceptions\UnsupportedModelException;

class CacheQueryBuilder implements BuilderInterface
{
    protected $model;

    protected $cacheKey;

    /**
     * @throws UnsupportedModelException
     */
    public function __construct(string $model)
    {
        if (!in_array(Cacheable::class, class_implements($model))) {
            throw new UnsupportedModelException();
        }

        $this->model = $model;

        $this->cacheKey = $this->model::primaryCacheKey();
    }

    public function find($value)
    {
        return Cache::remember($this->model::getCacheKey($value, $this->getCacheKey()), $this->model::cacheTimeout(), function () use ($value) {
            return $this->model::cacheWithRelation()->where($this->getCacheKey(), $value)->first();
        });
    }

    public function setCacheKey(string $key): BuilderInterface
    {
        $this->cacheKey = $key;

        return $this;
    }

    protected function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function findByKey($key, $value)
    {
        return Cache::remember($this->model::getCacheKey($value, $key), $this->model::cacheTimeout(), function () use ($value, $key) {
            return $this->model::cacheWithRelation()->where($key, $value)->first();
        });
    }


    public function get($ids): Collection
    {
        $ids = is_array($ids) ? $ids : [$ids];

        if (count($ids) == 0) return collect([]);

        $available = collect($this->availableFromCache($ids));

        $missing = collect($this->loadMissingItems($ids));

        return $available->merge($missing)->values();
    }

    public function all(): Collection
    {
        $ids = Cache::remember($this->model::getCacheKeyList(), $this->model::cacheTimeout(), function () {
            return $this->model::pluck($this->model::primaryCacheKey())->toArray();
        });

        return $this->get($ids);
    }

    public function when($condition, Closure $callback): CacheQueryBuilder
    {
        if ($condition) {
            $callback($this);
        }

        return $this;
    }

    protected function availableFromCache(array $ids)
    {
        $keys = array_map(function ($id) {
            return $this->model::getCacheKey($id);
        }, $ids);

        return Cache::many($keys);
    }

    protected function loadMissingItems($ids): Collection
    {
        $missingIds = $this->missingIds($ids);

        if (empty($missingIds)) return collect([]);

        $missingItems = $this->model::cacheWithRelation($missingIds)
            ->whereIn($this->model::primaryCacheKey(), $missingIds)
            ->get();

        foreach ($missingItems as $item) {
            Cache::put($this->model::getCacheKey($item->{$this->model::primaryCacheKey()}), $item, $this->model::cacheTimeout());
        }

        return $missingItems->mapWithKeys(function ($item) {
            return [$this->model::getCacheKey($item->{$this->model::primaryCacheKey()}) => $item];
        });
    }

    protected function missingIds($ids): array
    {
        return collect($ids)->filter(function ($id) {
            return Cache::missing($this->model::getCacheKey($id));
        })->toArray();
    }
}
