<?php

namespace Tests\Feature\Security;

use Tests\TestCase;

class BasicSecurityTest extends TestCase
{
    /**
     * Testa proteção contra SQL Injection
     */
    public function test_sql_injection_protection()
    {
        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE users--",
            "1 UNION SELECT * FROM users"
        ];
        
        foreach ($payloads as $payload) {
            $response = $this->post('/api/auth/login', [
                'email' => $payload,
                'password' => 'test'
            ]);
            
            $this->assertNotEquals(500, $response->status());
        }
    }
    
    /**
     * Testa proteção contra XSS
     */
    public function test_xss_protection()
    {
        $payloads = [
            "<script>alert('XSS')</script>",
            "javascript:alert('XSS')",
            "<img src=x onerror=alert('XSS')>"
        ];
        
        foreach ($payloads as $payload) {
            $response = $this->get('/?search=' . urlencode($payload));
            
            // Não deve conter script não escapado
            $this->assertStringNotContainsString('<script>', $response->content());
            $this->assertStringNotContainsString('javascript:', $response->content());
        }
    }
    
    /**
     * Testa headers de segurança
     */
    public function test_security_headers()
    {
        $response = $this->get('/');
        
        $response->assertHeader('X-Frame-Options');
        $response->assertHeader('X-Content-Type-Options');
        $response->assertHeader('X-XSS-Protection');
    }
}
