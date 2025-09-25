<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    /**
     * Cache com tags para invalidação eficiente
     */
    public static function rememberWithTags($tags, $key, $ttl, $callback)
    {
        // Se Redis disponível, usar tags
        if (config('cache.default') === 'redis') {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }
        
        // Fallback para cache normal
        return Cache::remember($key, $ttl, $callback);
    }
    
    /**
     * Invalidar cache por tags
     */
    public static function invalidateTags($tags)
    {
        if (config('cache.default') === 'redis') {
            Cache::tags($tags)->flush();
        }
    }
    
    /**
     * Cache de query com versionamento
     */
    public static function queryCache($model, $key, $ttl = 300)
    {
        $version = Cache::get("model_version_{$model}", 1);
        $cacheKey = "{$model}_{$version}_{$key}";
        
        return Cache::remember($cacheKey, $ttl, function() use ($model) {
            return $model::query();
        });
    }
    
    /**
     * Incrementar versão do model (invalida todos os caches)
     */
    public static function invalidateModel($model)
    {
        $version = Cache::get("model_version_{$model}", 1);
        Cache::put("model_version_{$model}", $version + 1, 86400);
    }
    
    /**
     * Cache warming - pré-carrega caches críticos
     */
    public static function warmCache()
    {
        // Pré-carregar dados mais acessados
        $criticalCaches = [
            'top_games' => function() {
                return \App\Models\Game::orderBy('views', 'desc')->limit(50)->get();
            },
            'categories' => function() {
                return \App\Models\Category::with('games')->get();
            },
            'providers' => function() {
                return \App\Models\Provider::where('active', 1)->get();
            },
            'settings' => function() {
                return \App\Models\Setting::all()->pluck('value', 'key');
            },
        ];
        
        foreach ($criticalCaches as $key => $callback) {
            Cache::put($key, $callback(), 3600);
        }
        
        return count($criticalCaches);
    }
}
