<?php

namespace Tests\Feature\Affiliate;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliatePanelBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_affiliate_login_page_uses_custom_theme(): void
    {
        $response = $this->get('/afiliado/login');

        $response->assertOk();
        $response->assertSee('css/custom-filament-theme.css', false);
        $response->assertSee('css/fontawesomepro.min.css', false);
    }

    public function test_affiliate_user_role_can_access_dashboard_with_branding(): void
    {
        $this->seed(AdminUserSeeder::class);

        $affiliate = User::whereEmail('afiliado@lucrativabet.com')->firstOrFail();

        $response = $this->actingAs($affiliate)->get('/afiliado/minha-dashboard');

        $response->assertOk();
        $response->assertSee('Programa de Afiliados', false);
        $response->assertSee('css/custom-filament-theme.css', false);
    }
}
