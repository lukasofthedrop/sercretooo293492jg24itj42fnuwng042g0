<?php

namespace Tests\Feature;

use Tests\TestCase;

class BasicSystemTest extends TestCase
{
    /**
     * Test if composer autoload works
     */
    public function test_composer_autoload_works()
    {
        $this->assertTrue(class_exists(\App\Models\User::class));
        $this->assertTrue(class_exists(\Illuminate\Support\Facades\DB::class));
    }

    /**
     * Test if config files are loaded
     */
    public function test_config_files_loaded()
    {
        $this->assertNotNull(config('app.name'));
        $this->assertNotNull(config('database.default'));
        $this->assertNotNull(config('cache.default'));
    }

    /**
     * Test if routes are loaded
     */
    public function test_routes_loaded()
    {
        $routes = app('router')->getRoutes();
        $this->assertNotEmpty($routes);
        $this->assertTrue($routes->count() > 0);
    }

    /**
     * Test if API health check works
     */
    public function test_api_health_check()
    {
        $response = $this->withoutMiddleware()->get('/api');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'API is running']);
    }

    /**
     * Test if Redis client is configured
     */
    public function test_redis_client_configured()
    {
        $this->assertEquals('predis', config('database.redis.client'));
    }

    /**
     * Test if critical files exist
     */
    public function test_critical_files_exist()
    {
        $this->assertFileExists(base_path('composer.json'));
        $this->assertFileExists(base_path('package.json'));
        $this->assertFileExists(base_path('.env'));
        $this->assertFileExists(base_path('artisan'));
    }

    /**
     * Test if vendor autoload is working
     */
    public function test_vendor_autoload()
    {
        $this->assertFileExists(base_path('vendor/autoload.php'));
        $this->assertTrue(class_exists(\Predis\Client::class));
    }

    /**
     * Test if storage directories are writable
     */
    public function test_storage_writable()
    {
        $this->assertTrue(is_writable(storage_path('app')));
        $this->assertTrue(is_writable(storage_path('logs')));
        $this->assertTrue(is_writable(storage_path('framework/cache')));
    }
}