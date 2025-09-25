<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AffiliateTools extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'AFILIADO';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Ferramentas';
    protected static ?string $title = 'Ferramentas de Marketing';
    protected static string $view = 'filament.pages.affiliate-tools';
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->inviter_code && $user->hasRole('afiliado');
    }
    
    public function mount(): void
    {
        $this->generateMarketingData();
    }
    
    protected function generateMarketingData(): void
    {
        $user = auth()->user();
        
        // Links personalizados para diferentes plataformas
        $this->linkWhatsapp = $this->generateWhatsAppLink();
        $this->linkTelegram = $this->generateTelegramLink();
        $this->linkTwitter = $this->generateTwitterLink();
        $this->linkFacebook = $this->generateFacebookLink();
        
        // Banners e materiais
        $this->banners = $this->generateBanners();
        
        // Textos prontos
        $this->copyTexts = $this->generateCopyTexts();
        
        // Código do afiliado
        $this->affiliateCode = $user->inviter_code;
        $this->inviteLink = url('/register?code=' . $user->inviter_code);
    }
    
    protected function generateWhatsAppLink(): string
    {
        $message = "🎰 Cadastre-se na LucrativaBet usando meu link e ganhe bônus exclusivos!\n\n";
        $message .= "✅ Bônus de boas-vindas\n";
        $message .= "✅ Promoções diárias\n";
        $message .= "✅ Saques rápidos\n\n";
        $message .= "👉 " . url('/register?code=' . auth()->user()->inviter_code);
        
        return "https://wa.me/?text=" . urlencode($message);
    }
    
    protected function generateTelegramLink(): string
    {
        $message = "🎰 *LucrativaBet - A melhor plataforma de jogos!*\n\n";
        $message .= "Cadastre-se com meu link especial:\n";
        $message .= url('/register?code=' . auth()->user()->inviter_code);
        
        return "https://t.me/share/url?url=" . urlencode(url('/register?code=' . auth()->user()->inviter_code)) . "&text=" . urlencode($message);
    }
    
    protected function generateTwitterLink(): string
    {
        $message = "🎰 Jogue e ganhe na @LucrativaBet! Use meu código especial para bônus exclusivos 🎁";
        
        return "https://twitter.com/intent/tweet?text=" . urlencode($message) . "&url=" . urlencode(url('/register?code=' . auth()->user()->inviter_code));
    }
    
    protected function generateFacebookLink(): string
    {
        return "https://www.facebook.com/sharer/sharer.php?u=" . urlencode(url('/register?code=' . auth()->user()->inviter_code));
    }
    
    protected function generateBanners(): array
    {
        return [
            [
                'size' => '728x90',
                'name' => 'Banner Horizontal',
                'preview' => '/images/banner-728x90.jpg',
                'code' => '<a href="' . url('/register?code=' . auth()->user()->inviter_code) . '"><img src="' . url('/images/banner-728x90.jpg') . '" alt="LucrativaBet" width="728" height="90"></a>'
            ],
            [
                'size' => '300x250',
                'name' => 'Banner Quadrado',
                'preview' => '/images/banner-300x250.jpg',
                'code' => '<a href="' . url('/register?code=' . auth()->user()->inviter_code) . '"><img src="' . url('/images/banner-300x250.jpg') . '" alt="LucrativaBet" width="300" height="250"></a>'
            ],
            [
                'size' => '160x600',
                'name' => 'Banner Vertical',
                'preview' => '/images/banner-160x600.jpg',
                'code' => '<a href="' . url('/register?code=' . auth()->user()->inviter_code) . '"><img src="' . url('/images/banner-160x600.jpg') . '" alt="LucrativaBet" width="160" height="600"></a>'
            ],
        ];
    }
    
    protected function generateCopyTexts(): array
    {
        $link = url('/register?code=' . auth()->user()->inviter_code);
        
        return [
            [
                'title' => 'Texto Casual',
                'text' => "Galera, descobri uma plataforma de jogos incrível! 🎰 A LucrativaBet tem os melhores jogos e paga super rápido. Cadastre-se com meu link e ganhe bônus: {$link}"
            ],
            [
                'title' => 'Texto Profissional',
                'text' => "Conheça a LucrativaBet, plataforma líder em jogos online no Brasil. Oferecemos variedade de jogos, pagamentos rápidos e suporte 24/7. Cadastre-se através do link: {$link}"
            ],
            [
                'title' => 'Texto para Stories',
                'text' => "🎰 OPORTUNIDADE!\n\n✅ Bônus de cadastro\n✅ Jogos exclusivos\n✅ Saques em minutos\n\nArrasta pra cima 👆\n{$link}"
            ],
            [
                'title' => 'Texto para Grupo',
                'text' => "Fala pessoal! 👋\n\nQuem curte uns jogos online? A LucrativaBet tá com promoções imperdíveis essa semana!\n\n🎁 Bônus no primeiro depósito\n💰 Cashback semanal\n⚡ Saque via PIX\n\nSe cadastra pelo meu link: {$link}"
            ],
        ];
    }
    
    protected function getViewData(): array
    {
        return [
            'linkWhatsapp' => $this->linkWhatsapp,
            'linkTelegram' => $this->linkTelegram,
            'linkTwitter' => $this->linkTwitter,
            'linkFacebook' => $this->linkFacebook,
            'banners' => $this->banners,
            'copyTexts' => $this->copyTexts,
            'affiliateCode' => $this->affiliateCode,
            'inviteLink' => $this->inviteLink,
        ];
    }
}