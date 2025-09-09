<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemHealthTest extends TestCase
{
    /**
     * Test if homepage loads
     */
    public function test_homepage_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test if admin login page loads
     */
    public function test_admin_login_loads()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    /**
     * Test if API is responding
     */
    public function test_api_responds()
    {
        // Test API health endpoint instead of specific route
        $response = $this->get('/api');
        $response->assertStatus(200)->assertJson(['message' => 'API is running']);
    }

    /**
     * Test if database connection works
     */
    public function test_database_connection()
    {
        // Just check that we can connect to database and users table exists
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    /**
     * Test if Redis cache works
     */
    public function test_redis_cache_works()
    {
        cache()->put('test_key', 'test_value', 60);
        $this->assertEquals('test_value', cache()->get('test_key'));
        cache()->forget('test_key');
    }

    /**
     * Test if configuration is correct
     */
    public function test_configuration()
    {
        $this->assertEquals('redis', config('cache.default'));
        $this->assertEquals('redis', config('session.driver'));
        $this->assertEquals('redis', config('queue.default'));
    }
}