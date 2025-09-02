<?php

namespace App\Filament\Pages;

use App\Helpers\Core;
use App\Models\CustomLayout;

use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LayoutCssCustom extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.layout-css-custom';
    protected static ?string $navigationLabel = 'Customização Layout';
    protected static ?string $modelLabel = 'Customização Layout';
    protected static ?string $title = 'Customização Layout';
    protected static ?string $slug = 'custom-layout';

    public ?array $data = [];
    public CustomLayout $custom;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla o acesso total à página
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla a visualização de elementos específicos
    }

    public function mount(): void 
    {
        $this->custom = CustomLayout::first();
        $data = $this->custom->toArray();
        $this->form->fill($data);
    }
    



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configurações')
                    ->tabs([
                        // ABA 1: SISTEMA & OTIMIZAÇÃO
                        Tabs\Tab::make('🚀 SISTEMA')
                            ->schema([
                                $this->getClearCacheSection(),
                                $this->css_do_geral(),
                                $this->getSectionCustomCode(),
                            ]),

                        // ABA 2: LAYOUT PRINCIPAL
                        Tabs\Tab::make('🏠 LAYOUT PRINCIPAL')
                            ->schema([
                                $this->css_do_navbar(),
                                $this->css_do_sidebar(),
                                $this->css_do_homepage(),
                                $this->css_do_footer(),
                            ]),

                        // ABA 3: PÁGINAS DE USUÁRIO
                        Tabs\Tab::make('👤 PÁGINAS DE USUÁRIO')
                            ->schema([
                                $this->css_do_login_registro_esquci(),
                                $this->css_do_myconta(),
                                $this->central_suporte(),
                            ]),

                        // ABA 4: CARTEIRA & TRANSAÇÕES
                        Tabs\Tab::make('💰 CARTEIRA & TRANSAÇÕES')
                            ->schema([
                                $this->css_do_WalletDashboard(),
                                $this->css_do_WalletBalance(),
                                $this->css_do_WalletDeposit(),
                                $this->css_do_PixWallet(),
                                $this->css_do_WalletWithdrawal(),
                                $this->css_do_BetHistory(),
                            ]),

                        // ABA 5: GAMIFICAÇÃO & PROMOÇÕES
                        Tabs\Tab::make('🎁 GAMIFICAÇÃO & PROMOÇÕES')
                            ->schema([
                                $this->css_do_bonus_diario(),
                                $this->css_do_missoes(),
                                $this->css_do_vips(),
                                $this->css_do_promocoes(),
                                $this->css_do_affiliates(),
                            ]),

                        // ABA 6: JOGOS & CONTEÚDO
                        Tabs\Tab::make('🎮 JOGOS & CONTEÚDO')
                            ->schema([
                                $this->css_do_listgames(),
                                $this->css_do_termos_sport(),
                            ]),

                        // ABA 7: MOBILE & DISPOSITIVOS
                        Tabs\Tab::make('📱 MOBILE & DISPOSITIVOS')
                            ->schema([
                                $this->css_do_menu_cell(),
                                $this->css_do_popup_cookies(),
                            ]),

                        // ABA 8: CONTEÚDO & CONFIGURAÇÕES
                        Tabs\Tab::make('📝 CONTEÚDO & CONFIGURAÇÕES')
                            ->schema([
                                $this->getSecTokenJivochat(),
                                $this->getSectionPlatformTexts(),
                                $this->getSectiimagensmanegem(),
                                $this->getSectilinkmagem(),
                            ]),
                    ])
            ])
            ->statePath('data');
    }
 


    protected function getClearCacheSection(): Section
    {
        return Section::make('🚀 Otimização & Cache')
            ->description('Controles principais do sistema - limpar cache, atualizar cores e gerenciar memória')
            ->schema([
                \Filament\Forms\Components\Placeholder::make('limpar_cache')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString(
                        '<div style="font-weight: 600; display: flex; align-items: center;">
                            <!-- Botão Limpar Cache -->
                            <a href="' . route('clear.cache') . '" class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #00ff00;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                               onclick="return confirm(\'Tem certeza de que deseja limpar o cache?\');">
                                LIMPAR CACHE
                            </a>
                    
                            <!-- Botão Atualizar Cores -->
                            <button onclick="updateColors()" class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 150px;
                                    display: flex;
                                    background-color: #00ff00;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                                    color: black;
                                    border: none;
                                    cursor: pointer;
                               ">
                                🎨 ATUALIZAR CORES
                            </button>
                    
                            <!-- Botão Limpar Memória -->
                            <a href="' . route('clear.memory') . '" class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #00ff00;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                               onclick="return confirm(\'Tem certeza de que deseja limpar a memória?\');">
                                LIMPAR MEMÓRIA
                            </a>
                    
                        </div>
                        
                        <script>
                        function updateColors() {
                            if (confirm("🎨 Aplicar cores (restart mínimo)?")) {
                                // Mostrar estado de carregamento
                                const btn = event.target;
                                const originalText = btn.innerHTML;
                                btn.innerHTML = "⏳ Atualizando...";
                                btn.disabled = true;
                                
                                // Fazer requisição AJAX
                                fetch("' . route('update.colors') . '", {
                                    method: "GET",
                                    headers: {
                                        "X-Requested-With": "XMLHttpRequest",
                                        "Accept": "application/json"
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Notificação de sucesso
                                        const notification = document.createElement("div");
                                        notification.style.cssText = `
                                            position: fixed;
                                            top: 20px;
                                            right: 20px;
                                            background-color: #10b981;
                                            color: white;
                                            padding: 15px 20px;
                                            border-radius: 8px;
                                            z-index: 9999;
                                            font-weight: 600;
                                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                                        `;
                                        notification.textContent = data.message;
                                        document.body.appendChild(notification);
                                        
                                        // Remover notificação após 3 segundos
                                        setTimeout(() => {
                                            notification.remove();
                                        }, 3000);
                                    }
                                })
                                .catch(error => {
                                    console.error("Erro:", error);
                                    alert("Erro ao atualizar cores!");
                                })
                                .finally(() => {
                                    // Restaurar botão
                                    btn.innerHTML = originalText;
                                    btn.disabled = false;
                                });
                            }
                        }
                        </script>'
                    )),
            ]);
    }
    
    protected function getSecTokenJivochat(): Section
    {
        return Section::make('💬 TOKEN JIVOCHAT')
            ->label('Configuração do chat ao vivo - altere o token do Jivochat')
            ->schema([
                \Filament\Forms\Components\Placeholder::make('limpar_cache')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString(
                        '<div style="font-weight: 600; display: flex; align-items: center;">
                            <!-- Botão Limpar Cache -->
                            <a href="https://www.jivochat.com.br/?partner_id=47634" class="dark:text-white" 
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 127px;
                                    display: flex;
                                    background-color: #00ff00;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               " 
                             );">
                                SITE DO CHAT
                            </a>
                
                    
                        </div>'
                    )),
                TextInput::make("token_jivochat")
                    ->label("Token do Jivochat Ex: //code.jivosite.com/widget/lmxxxxxxxx")
                    ->placeholder("Insira o token do Jivochat aqui Ex: //code.jivosite.com/widget/lmxxxxxxxx"),
            ])->columns(1);
    }
    


    protected function getSectilinkmagem(): Section
    {
        return Section::make('LINKS COMPLEMENTARES')
            ->label('Altere os links complementares')
            ->schema([
                TextInput::make("link_suporte")->label("Link do suporte"),
                TextInput::make("link_lincenca")->label("Link da Licença"),
                TextInput::make("link_app")->label("Link do App"),
                TextInput::make("link_telegram")->label("Link do Telegram"),
                TextInput::make("link_facebook")->label("Link do Facebook"),
                TextInput::make("link_whatsapp")->label("Link do WhatsApp"),
                TextInput::make("link_instagram")->label("Link do Instagram"),
            ])->columns(3);
    }

    protected function getSectiimagensmanegem(): Section
    {
        return Section::make('banner e imagens')
            ->label('Imagens e Banners')
            ->schema([
                FileUpload::make('image_hot4')->label("Imagem do banner licença")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('banner_deposito1')->label("Banner do depósito")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('banner_deposito2')->label("Banner do QR Code")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('banner_registro')->label("Banner do Registrar")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('banner_login')->label("Banner do Login")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('menucell_inicio')->label("Imagem cell do Menu Início")->placeholder('Carregue uma imagem')->image(),                
                FileUpload::make('menucell_suporte')->label("Imagem cell do Menu Suporte")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('menucell_carteira')->label("Imagem cell do Menu Carteira")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('menucell_afiliado')->label("Imagem cell do Menu Afiliado")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('menucell_saque')->label("Imagem cell do Menu Saque")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('menucell_sair')->label("Imagem  cell do Menu Sair")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_imagen1')->label("Imagem do Footer 1")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_imagen2')->label("Imagem do Footer 2")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_imagen3')->label("Imagem do Footer 3")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_telegram')->label("Imagem do Footer Telegram")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_facebook')->label("Imagem do Footer Facebook")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_whatsapp')->label("Imagem do Footer WhatsApp")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_instagram')->label("Imagem do Footer Instagram")->placeholder('Carregue uma imagem')->image(),
                FileUpload::make('footer_mais18')->label("Imagem do Footer +18")->placeholder('Carregue uma imagem')->image(),
            ])->columns(4);
    }

    protected function getSectionCustomCode(): Section
    {
        return Section::make('⚙️ Código Customizado & Pixels')
            ->description('CSS, JavaScript personalizado e configuração de pixels de rastreamento')
            ->schema([
                TextInput::make('idPixelFC')->label("Id do Pixel Facebook"),
                TextInput::make('idPixelGoogle')->label("Id do Pixel Google"),
                CodeField::make('custom_css')->setLanguage(CodeField::CSS)->withLineNumbers()->minHeight(100),
                CodeField::make('custom_js')->setLanguage(CodeField::JS)->withLineNumbers()->minHeight(100),
            ]);
    }

    private function uploadFile($file)
    {
        // Se o arquivo for um caminho já existente (string), encapsula em um array.
        if (is_string($file)) {
            return [$file];
        }
    
        // Verifica se é um array ou objeto antes de tentar iterar
        if (!empty($file) && (is_array($file) || is_object($file))) {
            foreach ($file as $temporaryFile) {
                if ($temporaryFile instanceof TemporaryUploadedFile) {
                    // Chama o método Core::upload() para processar o arquivo
                    $path = Core::upload($temporaryFile);
    
                    // Verifica se o caminho foi retornado corretamente
                    return [$path['path'] ?? $temporaryFile];
                }
                return [$temporaryFile];
            }
        }
    
        // Se não for um array, objeto ou string válida, retorna null.
        return null;
    }





    /////////////////////////////////////////////////////////////////////
    ///////////////////////// CENTRAL DE DISIGN /////////////////////////
    /////////////////////////////////////////////////////////////////////


    // PAGINA NAVBAR   | FICA EM CIMA DO SITE
    protected function css_do_navbar(): Section
    {
        return Section::make("🔝 Navbar (Barra Superior)")
            ->description('Cores da barra de navegação superior - botões, texto, ícones, login/registro')
            ->label('Navbar')
            ->schema([
                ColorPicker::make('navbar_background')->label('Cor de fundo do Navbar')->required(),
                ColorPicker::make('navbar_text')->label('Cor do texto do Navbar')->required(),
                ColorPicker::make('navbar_icon_menu')->label('Cor do ícone do Menu')->required(),
                ColorPicker::make('navbar_icon_promocoes')->label('Cor do ícone de Promoções')->required(),
                ColorPicker::make('navbar_icon_promocoes_segunda_cor')->label('Segunda cor do ícone de Promoções')->required(),
                ColorPicker::make('navbar_icon_casino')->label('Cor do ícone do Casino')->required(),
                ColorPicker::make('navbar_icon_sport')->label('Cor do ícone de Esportes')->required(),
                ColorPicker::make('navbar_button_text_login')->label('Cor do texto do botão de Login')->required(),
                ColorPicker::make('navbar_button_text_registro')->label('Cor do texto do botão de Registro')->required(),
                ColorPicker::make('navbar_button_background_login')->label('Cor de fundo do botão de Login')->required(),
                ColorPicker::make('navbar_button_background_registro')->label('Cor de fundo do botão de Registro')->required(),
                ColorPicker::make('navbar_button_border_color')->label('Cor da borda do botão')->required(),
                ColorPicker::make('navbar_button_text_superior')->label('Cor do texto do botão superior')->required(),
                ColorPicker::make('navbar_button_background_superior')->label('Cor de fundo do botão superior')->required(),
                ColorPicker::make('navbar_text_superior')->label('Cor do texto superior')->required(),
                ColorPicker::make('navbar_button_deposito_background')->label('Cor de fundo do botão de Depósito')->required(),
                ColorPicker::make('navbar_button_deposito_text_color')->label('Cor do texto do botão de Depósito')->required(),
                ColorPicker::make('navbar_button_deposito_border_color')->label('Cor da borda do botão de Depósito')->required(),
                ColorPicker::make('navbar_button_deposito_píx_color_text')->label('Cor do texto do botão de Depósito PIX')->required(),
                ColorPicker::make('navbar_button_deposito_píx_background')->label('Cor de fundo do botão de Depósito PIX')->required(),
                ColorPicker::make('navbar_button_deposito_píx_icon')->label('Cor do ícone do botão de Depósito PIX')->required(),
                ColorPicker::make('navbar_button_carteira_background')->label('Cor de fundo do botão Carteira')->required(),
                ColorPicker::make('navbar_button_carteira_text_color')->label('Cor do texto do botão Carteira')->required(),
                ColorPicker::make('navbar_button_carteira_border_color')->label('Cor da borda do botão Carteira')->required(),
                ColorPicker::make('navbar_perfil_text_color')->label('Cor do texto do Perfil')->required(),
                ColorPicker::make('navbar_perfil_background')->label('Cor de fundo do Perfil')->required(),
                ColorPicker::make('navbar_perfil_icon_color')->label('Cor do ícone do Perfil')->required(),
                ColorPicker::make('navbar_perfil_icon_color_border')->label('Cor da borda do ícone do Perfil')->required(),
                ColorPicker::make('navbar_perfil_modal_icon_color')->label('Cor do ícone do Modal do Perfil')->required(),
                ColorPicker::make('navbar_perfil_modal_text_modal')->label('Cor do texto do Modal do Perfil')->required(),
                ColorPicker::make('navbar_perfil_modal_background_modal')->label('Cor de fundo do Modal do Perfil')->required(),
                ColorPicker::make('navbar_perfil_modal_hover_modal')->label('Cor do hover do Modal do Perfil')->required(),


            ])->columns(4);
    }
 

    // PAGINA SIDERBAR | FICA NA LATERAL DO SITE
    protected function css_do_sidebar(): Section
    {

        return Section::make("📋 Sidebar (Barra Lateral)")
            ->description('Cores da barra lateral esquerda - missões, VIP, promoções, ícones de jogos')
            ->label('Sidebar')
            ->schema([
            ColorPicker::make('sidebar_background')->label('Cor de fundo do Sidebar')->required(),
            ColorPicker::make('sidebar_button_missoes_background')->label('Cor de fundo do botão Missões')->required(),
            ColorPicker::make('sidebar_button_vip_background')->label('Cor de fundo do botão VIP')->required(),
            ColorPicker::make('sidebar_button_ganhe_background')->label('Cor de fundo do botão Promoçoes')->required(),
            ColorPicker::make('sidebar_button_bonus_background')->label('Cor de fundo do botão bonus')->required(),
            ColorPicker::make('sidebar_button_missoes_text')->label('Cor do texto do botão Missões')->required(),
            ColorPicker::make('sidebar_button_ganhe_text')->label('Cor do texto do botão Ganhe')->required(),
            ColorPicker::make('sidebar_button_vip_text')->label('Cor do texto do botão VIP')->required(),
            ColorPicker::make('sidebar_button_hover')->label('Cor do hover do botão')->required(),
            ColorPicker::make('sidebar_text_hover')->label('Cor do hover do texto')->required(),
            ColorPicker::make('sidebar_text_color')->label('Cor do texto')->required(),
            ColorPicker::make('sidebar_border')->label('Cor da borda')->required(),
            ColorPicker::make('sidebar_icons')->label('Cor dos ícones')->required(),
            ColorPicker::make('sidebar_icons_background')->label('Cor de fundo dos ícones')->required(),
            ])->columns(4);
    }
    // PAGINA HOMEPAGE | FICA NA PAGINA INICIAL DO SITE

    protected function css_do_homepage(): Section
    {
        return Section::make("Pagina de Início")
            ->description('Você pode alterar as cores do HomePage')
            ->label('HomePage')
            ->schema([
                ColorPicker::make('home_text_color')->label('Cor do texto da Home')->required(),
                ColorPicker::make('home_background')->label('Cor de fundo da Home')->required(),
                ColorPicker::make('home_background_button_banner')->label('Cor de fundo do botão Banner')->required(),
                ColorPicker::make('home_icon_color_button_banner')->label('Cor do ícone do botão Banner')->required(),
                
                ColorPicker::make('home_background_input_pesquisa')->label('Cor de fundo do Input de Pesquisa')->required(),
                ColorPicker::make('home_icon_color_input_pesquisa')->label('Cor do ícone do Input de Pesquisa')->required(),
                ColorPicker::make('home_border_color_input_pesquisa')->label('Cor da borda do Input de Pesquisa')->required(),

                ColorPicker::make('topo_icon_color')->label('Cor do ícone do botão De volta ao topo')->required(),
                ColorPicker::make('topo_button_text_color')->label('Cor do texto do botão De volta ao topo')->required(),
                ColorPicker::make('topo_button_background')->label('Cor de fundo do botão De volta ao topo')->required(),
                ColorPicker::make('topo_button_border_color')->label('Cor da borda do botão De volta ao topo"')->required(),


                ColorPicker::make('home_background_categorias')->label('Cor de fundo das Categorias')->required(),
                ColorPicker::make('home_text_color_categorias')->label('Cor do texto das Categorias')->required(),
                ColorPicker::make('home_background_pesquisa')->label('Cor de fundo da Pesquisa')->required(),
                ColorPicker::make('home_text_color_pesquisa')->label('Cor do texto da Pesquisa')->required(),
                ColorPicker::make('home_background_pesquisa_aviso')->label('Cor de fundo do Aviso de Pesquisa')->required(),
                ColorPicker::make('home_text_color_pesquisa_aviso')->label('Cor do texto do Aviso de Pesquisa')->required(),
                ColorPicker::make('home_background_button_pesquisa')->label('Cor de fundo do botão X de Pesquisa')->required(),
                ColorPicker::make('home_icon_color_button_pesquisa')->label('Cor do ícone do botão X de Pesquisa')->required(),
                ColorPicker::make('home_background_button_vertodos')->label('Cor de fundo do botão Ver Todos')->required(),
                ColorPicker::make('home_text_color_button_vertodos')->label('Cor do texto do botão Ver Todos')->required(),
                ColorPicker::make('home_background_button_jogar')->label('Cor de fundo do botão Jogar')->required(),
                ColorPicker::make('home_text_color_button_jogar')->label('Cor do texto do botão Jogar')->required(),
                ColorPicker::make('home_icon_color_button_jogar')->label('Cor do ícone do botão Jogar')->required(),
                ColorPicker::make('home_hover_jogar')->label('Cor do hover do botão Jogar')->required(),
            ])->columns(4);
    }




    // PAGINA FOOTER   | FICA EM BAIXO DO SITE

    protected function css_do_footer(): Section
    {

        return Section::make("Pagina de Rodapé")
            ->description('Você pode alterar as cores do rodapé')
            ->label('Rodapé')
            ->schema([
            ColorPicker::make('footer_background')->label('Cor de fundo do Rodapé')->required(),
            ColorPicker::make('footer_text_color')->label('Cor do Texto do Rodapé')->required(),
            ColorPicker::make('footer_links')->label('Links do Rodapé')->required(),
            ColorPicker::make('footer_button_background')->label('Cor de fundo do Botão do Rodapé')->required(),
            ColorPicker::make('footer_button_text')->label('Cor Texto do Botão do Rodapé')->required(),
            ColorPicker::make('footer_button_border')->label('Cor da Borda do Botão do Rodapé')->required(),
            ColorPicker::make('footer_icons')->label('Cor dos Ícones do Rodapé')->required(),
            ColorPicker::make('footer_titulos')->label('Cor dos Títulos do Rodapé')->required(),
            ColorPicker::make('footer_button_hover_language')->label('Cor do Hover do Botão do Rodapé')->required(),
            ColorPicker::make('footer_button_color_language')->label('Cor do Texto do Botão do Rodapé (Idioma)')->required(),
            ColorPicker::make('footer_button_background_language')->label('Cor de Fundo do Botão do Rodapé (Idioma)')->required(),
            ColorPicker::make('footer_button_border_language')->label('Cor da Borda do Botão do Rodapé (Idioma)')->required(),
            ColorPicker::make('footer_background_language')->label('Cor de Fundo do Rodapé (Idioma)')->required(),
            ])->columns(4);
    }
    // PAGINA DE TERMOS E SPORT
    protected function css_do_termos_sport(): Section
    {
        return Section::make("Pagina do Termos e Sport")
            ->description('Você pode alterar as cores da página de Termos e Sport')
            ->label('Termos e Sport')
            ->schema([
                ColorPicker::make('aviso_sport_background')->label('Cor de fundo do aviso Sport')->required(),
                ColorPicker::make('aviso_sport_text_color')->label('Cor do texto do aviso Sport')->required(),
                ColorPicker::make('titulo_principal_termos')->label('Cor do título principal dos Termos')->required(),
                ColorPicker::make('titulo_segundario_termos')->label('Cor do título secundário dos Termos')->required(),
                ColorPicker::make('descriçao_segundario_termos')->label('Cor da descrição secundária dos Termos')->required(),
            ])->columns(2);
    }


    // Modal MINHA CONTA | FICA NA PAGINA DE MINHA CONTA
    protected function css_do_myconta(): Section
    {
        return Section::make("Modal Minha Conta")
            ->description('Você pode alterar as cores da página Minha Conta')
            ->label('Minha Conta')
            ->schema([
                ColorPicker::make('myconta_background')->label('Cor de fundo da Minha Conta')->required(),
                ColorPicker::make('myconta_sub_background')->label('Cor de fundo secundária da Minha Conta')->required(),
                ColorPicker::make('myconta_text_color')->label('Cor do texto da Minha Conta')->required(),
                ColorPicker::make('myconta_button_background')->label('Cor de fundo do botão da Minha Conta')->required(),
                ColorPicker::make('myconta_button_icon_color')->label('Cor do ícone do botão da Minha Conta')->required(),
            ])->columns(2);
    }

    // PAGINA CENTRAL SUPORTE | FICA NA PAGINA DE CENTRAL DE SUPORTE
    protected function central_suporte(): Section
    {
        return Section::make('Central de Suporte')
            ->description('Você pode alterar as cores e estilos da Central de Suporte')
            ->schema([
                ColorPicker::make('central_suporte_background')->label('Fundo da Central de Suporte')->required(),
                ColorPicker::make('central_suporte_sub_background')->label('Sub Fundo')->required(),
                ColorPicker::make('central_suporte_button_background_ao_vivo')->label('Fundo do Botão Ao Vivo')->required(),
                ColorPicker::make('central_suporte_button_texto_ao_vivo')->label('Texto do Botão Ao Vivo')->required(),
                ColorPicker::make('central_suporte_button_icon_ao_vivo')->label('Ícone do Botão Ao Vivo')->required(),
                ColorPicker::make('central_suporte_button_background_denuncia')->label('Fundo do Botão de Denúncia')->required(),
                ColorPicker::make('central_suporte_button_texto_denuncia')->label('Texto do Botão de Denúncia')->required(),
                ColorPicker::make('central_suporte_button_icon_denuncia')->label('Ícone do Botão de Denúncia')->required(),
                ColorPicker::make('central_suporte_title_text_color')->label('Cor do Título')->required(),
                ColorPicker::make('central_suporte_icon_title_text_color')->label('Cor do Ícone do Título')->required(),
                ColorPicker::make('central_suporte_info_text_color')->label('Cor do Texto de Informação')->required(),
                ColorPicker::make('central_suporte_info_icon_color')->label('Cor do Ícone de Informação')->required(),
                ColorPicker::make('central_suporte_aviso_title_color')->label('Cor do Título do Aviso')->required(),
                ColorPicker::make('central_suporte_aviso_text_color')->label('Cor do Texto do Aviso')->required(),
                ColorPicker::make('central_suporte_aviso_text_negrito_color')->label('Cor do Texto Negrito do Aviso')->required(),
                ColorPicker::make('central_suporte_aviso_icon_color')->label('Cor do Ícone do Aviso')->required(),
            ])->columns(2);
    }
    // PAGINA LOGIN - RESGISTRO E ESQUECI A SENHA

    protected function css_do_login_registro_esquci(): Section
    {
        return Section::make("Página Login, Registro e Esqueci a Senha")
            ->description('Você pode alterar as cores das páginas de Login, Registro e Esqueci a Senha')
            ->label('Login, Registro e Esqueci a Senha')
            ->schema([
                ColorPicker::make('register_background')->label('Cor de fundo do Registro')->required(),
                ColorPicker::make('register_text_color')->label('Cor do texto do Registro')->required(),
                ColorPicker::make('register_links_color')->label('Cor dos links do Registro')->required(),
                ColorPicker::make('register_input_background')->label('Cor de fundo do Input do Registro')->required(),
                ColorPicker::make('register_input_text_color')->label('Cor do texto do Input do Registro')->required(),
                ColorPicker::make('register_input_border_color')->label('Cor da borda do Input do Registro')->required(),
                ColorPicker::make('register_button_text_color')->label('Cor do texto do botão do Registro')->required(),
                ColorPicker::make('register_button_background')->label('Cor de fundo do botão do Registro')->required(),
                ColorPicker::make('register_button_border_color')->label('Cor da borda do botão do Registro')->required(),
                ColorPicker::make('register_security_color')->label('Cor do bloco de segurança do Registro')->required(),
                ColorPicker::make('register_security_background')->label('Cor de fundo do bloco de segurança do Registro')->required(),
                ColorPicker::make('register_security_border_color')->label('Cor da borda de segurança do Registro')->required(),
                ColorPicker::make('geral_icons_color')->label('Cor dos ícones gerais')->required(),



                ColorPicker::make('login_background')->label('Cor de fundo do Login')->required(),
                ColorPicker::make('login_text_color')->label('Cor do texto do Login')->required(),
                ColorPicker::make('login_links_color')->label('Cor dos links do Login')->required(),
                ColorPicker::make('login_input_background')->label('Cor de fundo do Input do Login')->required(),
                ColorPicker::make('login_input_text_color')->label('Cor do texto do Input do Login')->required(),
                ColorPicker::make('login_input_border_color')->label('Cor da borda do Input do Login')->required(),
                ColorPicker::make('login_button_text_color')->label('Cor do texto do botão do Login')->required(),
                ColorPicker::make('login_button_background')->label('Cor de fundo do botão do Login')->required(),
                ColorPicker::make('login_button_border_color')->label('Cor da borda do botão do Login')->required(),

                ColorPicker::make('esqueci_background')->label('Cor de fundo do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_text_color')->label('Cor do texto do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_input_background')->label('Cor de fundo do Input do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_input_text_color')->label('Cor do texto do Input do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_input_border_color')->label('Cor da borda do Input do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_button_text_color')->label('Cor do texto do botão do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_button_background')->label('Cor de fundo do botão do Esqueci a Senha')->required(),
                ColorPicker::make('esqueci_button_border_color')->label('Cor da borda do botão do Esqueci a Senha')->required(),
            ])->columns(4);
    }


    // PAGINA LISTGAME | FICA NA PAGINA DE LISTA DE JOGOS

    protected function css_do_listgames(): Section
    {
        return Section::make("Página de Lista de Jogos")
            ->description('Você pode alterar as cores da Lista de Jogos')
            ->label('Lista de Jogos')
            ->schema([
                ColorPicker::make('gamelist_background')->label('Cor de fundo da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_input_background')->label('Cor de fundo do Input da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_input_text_color')->label('Cor do texto do Input da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_input_border_color')->label('Cor da borda do Input da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_text_color')->label('Cor do texto da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_button_background')->label('Cor de fundo do botão da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_button_text_color')->label('Cor do texto do botão da Lista de Jogos')->required(),
                ColorPicker::make('gamelist_button_border_color')->label('Cor da borda do botão da Lista de Jogos')->required(),
            ])->columns(4);
    }

    // PAGINA BONUS DIARIO | FICA NA PAGINA DE BONUS DIARIO

    protected function css_do_bonus_diario(): Section
    {
        return Section::make("Página de Bônus Diário")
            ->description('Você pode alterar as cores da página de Bônus Diário')
            ->label('Bônus Diário')
            ->schema([
                ColorPicker::make('bonus_diario_background')->label('Cor de fundo do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_sub_background')->label('Cor de fundo secundária do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_texto')->label('Cor do texto do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_texto_icon')->label('Cor do ícone do texto do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_texto_valor_bonus')->label('Cor do valor do bônus do texto do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_aviso')->label('Cor do aviso do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_botao_backgroud')->label('Cor de fundo do botão do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_botao_texto_color')->label('Cor do texto do botão do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_regras_title')->label('Cor do título das regras do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_regras_titulo')->label('Cor do título das regras do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_bola_interna')->label('Cor da bola interna do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_bola_fora_')->label('Cor da bola externa do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_bola_carregamento')->label('Cor da bola de carregamento do Bônus Diário')->required(),
                ColorPicker::make('bonus_diario_texto_bola')->label('Cor do texto da bola do Bônus Diário')->required(),
            ])->columns(4);
    }





    /////////////////////////////////////////////////////////////////////
    ////////////////////// CENTRAL DE DISIGN PT2 ////////////////////////
    /////////////////////////////////////////////////////////////////////

    // PAGINA CARTERA | FICA NA PAGINA DE CARTEIRA


    protected function css_do_WalletDashboard(): Section
    {
        return Section::make('Carteira Dashboard')
            ->description('Você pode alterar as cores do Dashboard da Carteira')
            ->label('Carteira Dashboard')
            ->schema([
                ColorPicker::make('carteira_background')->label('Cor de fundo da Carteira')->required(),
                ColorPicker::make('carteira_button_background')->label('Cor de fundo do botão da Carteira')->required(),
                ColorPicker::make('carteira_button_text_color')->label('Cor do texto do botão da Carteira')->required(),
                ColorPicker::make('carteira_button_border_color')->label('Cor da borda do botão da Carteira')->required(),
                ColorPicker::make('carteira_icon_color')->label('Cor dos ícones da Carteira')->required(),
                ColorPicker::make('carteira_text_color')->label('Cor do texto da Carteira')->required(),
            ])->columns(4);
    }



    protected function css_do_WalletBalance(): Section
    {
        return Section::make('Carteira de Saldo')
            ->description('Você pode alterar as cores do saldo da Carteira')
            ->label('Saldo da Carteira')
            ->schema([
                ColorPicker::make('carteira_saldo_background')->label('Cor de fundo do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_text_color')->label('Cor do texto do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_border_color')->label('Cor da borda do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_title_color')->label('Cor do título do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_icon_color')->label('Cor dos ícones do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_number_color')->label('Cor dos números do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_background')->label('Cor de fundo do botão de Depósito do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_button_saque_background')->label('Cor de fundo do botão de Saque do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_button_deposito_text_color')->label('Cor do texto do botão de Depósito do saldo da Carteira')->required(),
                ColorPicker::make('carteira_saldo_button_saque_text_color')->label('Cor do texto do botão de Saque do saldo da Carteira')->required(),
            ])->columns(3);
    }



    protected function css_do_WalletDeposit(): Section
    {
        return Section::make('Carteira de Depósito')
            ->description('Você pode alterar as cores do depósito da Carteira')
            ->label('Depósito da Carteira')
            ->schema([
                ColorPicker::make('carteira_deposito_background')->label('Cor de fundo do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_contagem_background')->label('Cor de fundo da contagem do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_contagem_text_color')->label('Cor do texto da contagem do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_contagem_number_color')->label('Cor dos números da contagem do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_contagem_quadrado_background')->label('Cor de fundo do quadrado da contagem do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_input_background')->label('Cor de fundo do Input do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_input_text_color')->label('Cor do texto do Input do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_input_number_color')->label('Cor dos números do Input do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_input_border_color')->label('Cor da borda do Input do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_title_color')->label('Cor do título do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_number_color')->label('Cor dos números do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_number_background')->label('Cor de fundo dos números do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_button_background')->label('Cor de fundo do botão do Depósito da Carteira')->required(),
                ColorPicker::make('carteira_deposito_button_text_color')->label('Cor do texto do botão do Depósito da Carteira')->required(),
            ])->columns(3);
    }




    protected function css_do_PixWallet(): Section
    {
        return Section::make('Carteira PIX Copia e Cola')
            ->description('Você pode alterar as cores da Carteira PIX Copia e Cola')
            ->label('Carteira PIX Copia e Cola')
            ->schema([
                ColorPicker::make('carteira_saldo_pix_Texot_color')->label('Cor do texto do saldo PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_sub_text_color')->label('Cor do subtexto do saldo copiar PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_button_background')->label('Cor de fundo do botão do copiar PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_button_text_color')->label('Cor do texto do botão do copiar PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_input_background')->label('Cor de fundo do input do PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_input_text_color')->label('Cor do texto do input do PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_border_color')->label('Cor da borda do copiar PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_icon_color')->label('Cor dos ícones do copiar PIX da Carteira')->required(),
                ColorPicker::make('carteira_saldo_pix_background')->label('Cor de fundo do copiar PIX da Carteira')->required(),
            ])->columns(2);
    }






    protected function css_do_WalletWithdrawal(): Section
    {
        return Section::make('Carteira de Saque')
            ->description('Você pode alterar as cores do saque da Carteira')
            ->label('Saque da Carteira')
            ->schema([
                ColorPicker::make('carteira_saque_background')->label('Cor de fundo do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_text_color')->label('Cor do texto do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_number_color')->label('Cor dos números do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_input_background')->label('Cor de fundo do Input do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_input_text_color')->label('Cor do texto do Input do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_input_border_color')->label('Cor da borda do Input do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_button_text_color')->label('Cor do texto do botão do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_button_background')->label('Cor de fundo do botão do Saque da Carteira')->required(),
                ColorPicker::make('carteira_saque_icon_color')->label('Cor dos ícones do Saque da Carteira')->required(),
            ])->columns(2);
    }



    protected function css_do_BetHistory(): Section
    {
        return Section::make('Histórico de Apostas')
            ->description('Você pode alterar as cores do Histórico de Apostas')
            ->label('Histórico de Apostas')
            ->schema([
                ColorPicker::make('carteira_history_background')->label('Cor de fundo do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_title_color')->label('Cor do título do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_text_color')->label('Cor do texto do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_number_color')->label('Cor dos números do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_status_finalizado_color')->label('Cor do status finalizado do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_status_finalizado_background')->label('Cor de fundo do status finalizado do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_status_pedente_color')->label('Cor do status pendente do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_status_pedente_background')->label('Cor de fundo do status pendente do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_barra_color')->label('Cor da barra do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_barra_text_color')->label('Cor do texto da barra do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_icon_color')->label('Cor dos ícones do Histórico da Carteira')->required(),
                ColorPicker::make('carteira_history_barra_background')->label('Cor de fundo da barra do Histórico da Carteira')->required(),
            ])->columns(4);
    }



    // PAGINA DE AFILIADOS | FICA NA PAGINA DE AFILIADOS


    protected function css_do_affiliates(): Section
    {
        return Section::make("Página de Afiliados")
            ->description('Você pode alterar as cores da página de Afiliados')
            ->label('Afiliados')
            ->schema([
                ColorPicker::make('affiliates_background')->label('Cor de fundo dos Afiliados')->required(),
                ColorPicker::make('affiliates_sub_background')->label('Cor de fundo secundária dos Afiliados')->required(),
                ColorPicker::make('affiliates_text_color')->label('Cor do texto dos Afiliados')->required(),
                ColorPicker::make('affiliates_numero_color')->label('Cor do número dos Afiliados')->required(),
                ColorPicker::make('affiliates_button_saque_background')->label('Cor de fundo do botão de Saque')->required(),
                ColorPicker::make('affiliates_button_saque_text')->label('Cor do texto do botão de Saque')->required(),
                ColorPicker::make('affiliates_button_copie_background')->label('Cor de fundo do botão de Copiar')->required(),
                ColorPicker::make('affiliates_button_copie_text')->label('Cor do icon do botão de Copiar')->required(),
                ColorPicker::make('affiliates_icom_color')->label('Cor dos ícones dos Afiliados')->required(),
            ])->columns(4);
    }

    


    /////////////////////////////////////////////////////////////////////
    ////////////////////// CENTRAL DE DISIGN PT3 ////////////////////////
    /////////////////////////////////////////////////////////////////////

    
    // PAGINA VIP | FICA NA PAGINA DE VIP
    protected function css_do_vips(): Section
    {
        return Section::make("Página VIP")
            ->description('Você pode alterar as cores da página VIP')
            ->label('VIP')
            ->schema([
                ColorPicker::make('vips_background')->label('Cor de fundo dos VIPs')->required(),
                ColorPicker::make('vips_title_color')->label('Cor do título dos VIPs')->required(),
                ColorPicker::make('vips_text_color')->label('Cor do texto dos VIPs')->required(),
                ColorPicker::make('vips_description_color')->label('Cor da descrição dos VIPs')->required(),
                ColorPicker::make('vips_button_background')->label('Cor de fundo do botão dos VIPs')->required(),
                ColorPicker::make('vips_button_text_color')->label('Cor do texto do botão dos VIPs')->required(),
                ColorPicker::make('vips_progress_background')->label('Cor de fundo do progresso dos VIPs')->required(),
                ColorPicker::make('vips_progress_text_color')->label('Cor do texto do progresso dos VIPs')->required(),
                ColorPicker::make('vips_progress_prenchimento_background')->label('Cor de preenchimento do progresso dos VIPs')->required(),
                ColorPicker::make('vips_icons_text_color')->label('Cor do texto dos ícones dos VIPs')->required(),
                ColorPicker::make('vips_icons_background')->label('Cor de fundo dos ícones dos VIPs')->required(),
                ColorPicker::make('vips_icons_sub_text_color')->label('Cor do subtexto dos ícones dos VIPs')->required(),
                ColorPicker::make('vips_background_profile_vip')->label('Cor de fundo do perfil VIP')->required(),
                ColorPicker::make('vips_icon_mover_color')->label('Cor do ícone de mover dos VIPs')->required(),
                ColorPicker::make('vip_atual_background')->label('Cor de fundo do VIP atual')->required(),
                ColorPicker::make('vip_atual_text_color')->label('Cor do texto do VIP atual')->required(),
                ColorPicker::make('vip_proximo_background')->label('Cor de fundo do próximo VIP')->required(),
                ColorPicker::make('vip_proximo_text_color')->label('Cor do texto do próximo VIP')->required(),
            ])->columns(4);
    }


    

    // PAGINA DE MISSOES | FICA NA PAGINA DE MISSOES
    protected function css_do_missoes(): Section
    {
        return Section::make("Página de Missões")
            ->description('Você pode alterar as cores da página de Missões')
            ->label('Missões')
            ->schema([
                ColorPicker::make('missoes_background')->label('Cor de fundo das Missões')->required(),
                ColorPicker::make('missoes_sub_background')->label('Cor de fundo secundária das Missões')->required(),
                ColorPicker::make('missoes_text_color')->label('Cor do texto das Missões')->required(),
                ColorPicker::make('missoes_title_color')->label('Cor do título das Missões')->required(),
                ColorPicker::make('missoes_bonus_background')->label('Cor de fundo do bônus das Missões')->required(),
                ColorPicker::make('missoes_bonus_text_color')->label('Cor do texto do bônus das Missões')->required(),
                ColorPicker::make('missoes_button_background')->label('Cor de fundo do botão das Missões')->required(),
                ColorPicker::make('missoes_button_text_color')->label('Cor do texto do botão das Missões')->required(),
                ColorPicker::make('missoes_barraprogresso_background')->label('Cor de fundo da barra de progresso das Missões')->required(),
                ColorPicker::make('missoes_barraprogresso_prenchimento_background')->label('Cor de preenchimento da barra de progresso das Missões')->required(),
                ColorPicker::make('missoes_barraprogresso_text_color')->label('Cor do texto da barra de progresso das Missões')->required(),
            ])->columns(4);
    }

    

    // PAGINA DE PROMOÇÕES | FICA NA PAGINA DE PROMOÇÕES
    protected function css_do_promocoes(): Section
    {
        return Section::make("Página de Promoções")
            ->description('Você pode alterar as cores da página de Promoções')
            ->label('Promoções')
            ->schema([
                ColorPicker::make('promocoes_background')->label('Cor de fundo das Promoções')->required(),
                ColorPicker::make('promocoes_title_color')->label('Cor do título das Promoções')->required(),
                ColorPicker::make('promocoes_text_color')->label('Cor do texto das Promoções')->required(),
                ColorPicker::make('promocoes_sub_background')->label('Cor de fundo secundária das Promoções')->required(),
                ColorPicker::make('promocoes_button_background')->label('Cor de fundo do botão das Promoções')->required(),
                ColorPicker::make('promocoes_button_text_color')->label('Cor do texto do botão das Promoções')->required(),
                ColorPicker::make('promocoes_pupup_background')->label('Cor de fundo do popup das Promoções')->required(),
                ColorPicker::make('promocoes_pupup_text_color')->label('Cor do texto do popup das Promoções')->required(),
                ColorPicker::make('promocoes_icon_color')->label('Cor dos ícones das Promoções')->required(),
            ])->columns(4);
    }


    // PAGINA DE POPUP DE COOKIES | FICA NA PAGINA DE POPUP DE COOKIES
    protected function css_do_popup_cookies(): Section
    {
        return Section::make("Popup de Cookies")
            ->description('Você pode alterar as cores do Popup de Cookies')
            ->label('Popup de Cookies')
            ->schema([
                ColorPicker::make('popup_cookies_background')->label('Cor de fundo do Popup de Cookies')->required(),
                ColorPicker::make('popup_cookies_text_color')->label('Cor do texto do Popup de Cookies')->required(),
                ColorPicker::make('popup_cookies_button_background')->label('Cor de fundo do botão do Popup de Cookies')->required(),
                ColorPicker::make('popup_cookies_button_text_color')->label('Cor do texto do botão do Popup de Cookies')->required(),
                ColorPicker::make('popup_cookies_button_border_color')->label('Cor da borda do botão do Popup de Cookies')->required(),
            ])->columns(2);
    }

    // PAGINA DE MENU CELL | FICA NA PAGINA DE MENU CELULAR
    protected function css_do_menu_cell(): Section
    {
        return Section::make("Página de Menu Cell")
            ->description('Você pode alterar as cores do Menu Cell')
            ->label('Menu Cell')
            ->schema([
                ColorPicker::make('menu_cell_background')->label('Cor de fundo do Menu Cell')->required(),
                ColorPicker::make('menu_cell_text_color')->label('Cor do texto do Menu Cell')->required(),
            ])->columns(2);
    }




    // GERAL
    protected function css_do_geral(): Section
    {
        return Section::make("🎨 Cores Gerais do Sistema")
            ->description('Cores de fundo e texto que afetam todo o sistema')
            ->label('Cores Gerais')
            ->schema([
                ColorPicker::make('background_geral')->label('Cor de fundo geral')->required(),
                ColorPicker::make('background_geral_text_color')->label('Cor do texto geral')->required(),
            ])->columns(2);
    }

    // CARREGANDO
    protected function css_do_carregando(): Section
    {
        return Section::make("Carregando")
            ->description('Você pode alterar as cores da tela de carregamento')
            ->label('Carregando')
            ->schema([
                ColorPicker::make('carregando_background')->label('Cor de fundo do carregamento')->required(),
                ColorPicker::make('carregando_text_color')->label('Cor do texto do carregamento')->required(),
            ])->columns(2);
    }

    /////////////////////////////////////////////////////////////////////
    ////////////////////// CENTRAL DE DISIGN PT4 ////////////////////////
    /////////////////////////////////////////////////////////////////////

    // PAGINA DE TERMOS E CONDIÇÕES | FICA NA PAGINA DE TERMOS E CONDIÇÕES

    // PAGINA DE POLITICA DE PRIVACIDADE | FICA NA PAGINA DE POLITICA DE PRIVACIDADE

    // PAGINA DE POLITICA DE COOKIES | FICA NA PAGINA DE POLITICA DE COOKIES

    // PAGINA DE TERMO DE BONUS | FICA NA PAGINA DE TERMO DE BONUS



    /////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////
    ////////////////////// CENTRAL DE TEXTOS ////////////////////////
    /////////////////////////////////////////////////////////////////////
    protected function getSectionPlatformTexts(): Section
    {
        return Section::make('TEXTOS DA PLATAFORMA')
            ->label('Altere os textos da plataforma')
            ->schema([
                TextInput::make('homepage_jogos_em_destaque')->label('Jogos em Destaque na Homepage'),
                TextInput::make('vip_titulo')->label('Título VIP'),
                TextInput::make('vip_descriçao')->label('Descrição VIP'),
                TextInput::make('vip_sub_texto')->label('Subtexto VIP'),
                TextInput::make('vip_sub_titulo')->label('Subtítulo VIP'),
            ])->columns(2);
    }





    public function submit(): void
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()->title('Atenção')->body('Você não pode realizar esta alteração na versão demo')->danger()->send();
                return;
            }

            $data = $this->form->getState();
            $this->handleFileUploads();



            if ($this->custom->update($data)) {
                Notification::make()
                    ->title('🎨 CORES ATUALIZADAS COM SUCESSO!')
                    ->body('Servidor reiniciando automaticamente para aplicar as novas cores... A página será recarregada em instantes!')
                    ->success()
                    ->duration(8000) // Mostra por 8 segundos
                    ->send();
                return ;
            }

            Notification::make()->title('Erro')->body('Erro interno!')->danger()->send();
        } catch (Halt $exception) {
            Notification::make()->title('Erro ao alterar dados!')->body('Erro ao alterar dados!')->danger()->send();
        }
    }

    private function handleFileUploads(): void
    {
        $this->data['image_hot4'] = $this->processFileUpload($this->data['image_hot4']);
        $this->data['banner_deposito1'] = $this->processFileUpload($this->data['banner_deposito1']);
        $this->data['banner_deposito2'] = $this->processFileUpload($this->data['banner_deposito2']);
        $this->data['banner_registro'] = $this->processFileUpload($this->data['banner_registro']);
        $this->data['banner_login'] = $this->processFileUpload($this->data['banner_login']);
        $this->data['menucell_inicio'] = $this->processFileUpload($this->data['menucell_inicio']);
        $this->data['menucell_carteira'] = $this->processFileUpload($this->data['menucell_carteira']);
        $this->data['menucell_suporte'] = $this->processFileUpload($this->data['menucell_suporte']);
        $this->data['menucell_afiliado'] = $this->processFileUpload($this->data['menucell_afiliado']);
        $this->data['menucell_saque'] = $this->processFileUpload($this->data['menucell_saque']);
        $this->data['menucell_sair'] = $this->processFileUpload($this->data['menucell_sair']);
        $this->data['footer_imagen1'] = $this->processFileUpload($this->data['footer_imagen1']);
        $this->data['footer_imagen2'] = $this->processFileUpload($this->data['footer_imagen2']);
        $this->data['footer_imagen3'] = $this->processFileUpload($this->data['footer_imagen3']);
        $this->data['footer_telegram'] = $this->processFileUpload($this->data['footer_telegram']);
        $this->data['footer_facebook'] = $this->processFileUpload($this->data['footer_facebook']);
        $this->data['footer_whatsapp'] = $this->processFileUpload($this->data['footer_whatsapp']);
        $this->data['footer_instagram'] = $this->processFileUpload($this->data['footer_instagram']);
        $this->data['footer_mais18'] = $this->processFileUpload($this->data['footer_mais18']);
    }
    
    
    private function processFileUpload($file)
    {
        // Se não houver arquivo (ou for null), retorna null sem tentar processar.
        if (!$file) {
            return null;
        }
    
        // Se o upload existir, processa o arquivo.
        return $this->uploadFile($file);
    }
}
