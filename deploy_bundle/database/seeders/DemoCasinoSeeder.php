<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomLayout;
use App\Models\Game;
use App\Models\Provider;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DemoCasinoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogPath = resource_path('data/casino-games.json');

        if (! File::exists($catalogPath)) {
            $this->command?->warn('Arquivo casino-games.json não encontrado, pulando seeder de demo.');
            return;
        }

        $payload = json_decode(File::get($catalogPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command?->error('Não foi possível decodificar casino-games.json: ' . json_last_error_msg());
            return;
        }

        $games = $payload['games']['data'] ?? [];

        if (empty($games)) {
            $this->command?->warn('Nenhum jogo encontrado no dataset estático.');
            return;
        }

        Setting::updateOrCreate(
            ['id' => 1],
            [
                'software_name' => 'LucrativaBet Demo',
                'software_description' => 'Cassino demonstrativo operando sem banco de produção.',
                'currency_code' => 'BRL',
                'decimal_format' => 'comma',
                'currency_position' => 'left',
                'prefix' => 'R$',
                'storage' => 'local',
                'min_deposit' => 10,
                'max_deposit' => 10000,
                'min_withdrawal' => 50,
                'max_withdrawal' => 5000,
                'ngr_percent' => 5,
                'revshare_percentage' => 40,
                'soccer_percentage' => 5,
                'initial_bonus' => 100,
                'rollover' => 5,
                'rollover_deposit' => 3,
                'suitpay_is_enable' => true,
                'stripe_is_enable' => false,
                'bspay_is_enable' => false,
            ]
        );

        CustomLayout::updateOrCreate(
            ['id' => 1],
            [
                'font_family_default' => "'Roboto Condensed', sans-serif",
                'primary_color' => '#22c55e',
                'primary_opacity_color' => '#22c55e63',
                'secundary_color' => '#0f172a',
                'gray_dark_color' => '#1f2937',
                'gray_light_color' => '#cbd5f5',
                'gray_medium_color' => '#64748b',
                'gray_over_color' => '#0f172a',
                'title_color' => '#ffffff',
                'text_color' => '#98a7b5',
                'sub_text_color' => '#94a3b8',
                'placeholder_color' => '#475569',
                'background_color' => '#0f172a',
                'border_radius' => '0.50rem',
                'background_geral' => '#101825',
                'background_geral_dark' => '#070b12',
                'input_primary' => '#1f2937',
                'input_primary_dark' => '#0b1018',
                'carousel_banners' => '#101420',
                'carousel_banners_dark' => '#070b12',
                'sidebar_color' => '#101420',
                'sidebar_color_dark' => '#070b12',
                'navtop_color' => '#101420',
                'navtop_color_dark' => '#070b12',
                'side_menu' => '#101420',
                'side_menu_dark' => '#070b12',
                'card_color' => '#101420',
                'card_color_dark' => '#070b12',
                'footer_color' => '#101420',
                'footer_color_dark' => '#070b12',
                'custom_css' => null,
                'custom_js' => null,
            ]
        );

        DB::transaction(function () use ($games) {
            foreach ($games as $entry) {
                $providerData = $entry['provider'] ?? null;

                if (! $providerData) {
                    continue;
                }

                $provider = Provider::updateOrCreate(
                    ['code' => $providerData['code'] ?? Str::slug($providerData['name'] ?? 'PROVIDER')],
                    [
                        'name' => $providerData['name'] ?? $providerData['code'] ?? 'Provedor',
                        'status' => $providerData['status'] ?? 1,
                        'rtp' => $providerData['rtp'] ?? 0,
                        'views' => $providerData['views'] ?? 0,
                    ]
                );

                $game = Game::updateOrCreate(
                    ['game_code' => $entry['game_code'] ?? $entry['id']],
                    [
                        'provider_id' => $provider->id,
                        'game_server_url' => $entry['game_server_url'] ?? null,
                        'game_id' => $entry['game_id'] ?? $entry['id'],
                        'game_name' => $entry['game_name'] ?? 'Jogo',
                        'game_type' => $entry['game_type'] ?? null,
                        'description' => $entry['description'] ?? null,
                        'cover' => $entry['cover'] ?? $entry['banner'] ?? null,
                        'status' => (int) ($entry['status'] ?? 1),
                        'technology' => $entry['technology'] ?? null,
                        'has_lobby' => (int) ($entry['has_lobby'] ?? 0),
                        'is_mobile' => (int) ($entry['is_mobile'] ?? 1),
                        'has_freespins' => (int) ($entry['has_freespins'] ?? 0),
                        'has_tables' => (int) ($entry['has_tables'] ?? 0),
                        'only_demo' => (int) ($entry['only_demo'] ?? 0),
                        'rtp' => $entry['rtp'] ?? 0,
                        'distribution' => $entry['distribution'] ?? 'play_fiver',
                        'views' => $entry['views'] ?? 0,
                        'is_featured' => (int) ($entry['is_featured'] ?? 0),
                        'show_home' => (int) ($entry['show_home'] ?? 1),
                        'original' => (int) ($entry['original'] ?? 0),
                    ]
                );

                $categories = $entry['categories'] ?? [];

                if (empty($categories)) {
                    continue;
                }

                $categoryIds = [];

                foreach ($categories as $categoryData) {
                    $slug = $categoryData['slug'] ?? Str::slug($categoryData['name'] ?? 'categoria');

                    $category = Category::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $categoryData['name'] ?? Str::headline($slug),
                            'description' => $categoryData['description'] ?? null,
                            'image' => $categoryData['image'] ?? null,
                        ]
                    );

                    $categoryIds[] = $category->id;
                }

                if (! empty($categoryIds)) {
                    $game->categories()->syncWithoutDetaching($categoryIds);
                }
            }
        });
    }
}
