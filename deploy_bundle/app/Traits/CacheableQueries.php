<?php

namespace App\Traits;

trait CacheableQueries
{
    protected function cacheQuery($query, $key, $ttl = 300)
    {
        return cache()->remember($key, $ttl, function() use ($query) {
            return $query->get();
        });
    }
    
    protected function clearQueryCache($key)
    {
        cache()->forget($key);
    }
}
