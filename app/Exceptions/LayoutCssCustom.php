<?php

namespace App\Filament\Pages;

use App\Helpers\Core;
use App\Models\CustomLayout;

use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
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

                $this->getSectmensagemhome(),
                $this->getSectionBackground(),
                $this->getSectionSidebarNavbarFooter(),
                $this->getSectionCss(),
                $this->getSectiimagensmanegem(),
                $this->getSectilinkmagem(),
                $this->getSectionCustomCode(),
                
            ])
            ->statePath('data');
    }



    protected function getSectionBackground(): Section
    {
        return Section::make('TEMA DA PLATAFORMA 02')
            ->label('Background')
            ->description('Você pode alterar as cores do tema da plataforma')
            ->schema([
                ColorPicker::make('background_base')->label('Background Principal')->required(),
                ColorPicker::make('background_base_dark')->label('Background Principal (Dark)')->required(),
                ColorPicker::make('carousel_banners')->label('Carousel Banners')->required(),
                ColorPicker::make('carousel_banners_dark')->label('Carousel Banners (Dark)')->required(),
            ])->columns(4);
    }

    protected function getSectionSidebarNavbarFooter(): Section
    {
        return Section::make()
            ->label('Sidebar & Navbar & Footer')
            ->schema([
                ColorPicker::make('sidebar_color')->label('Sidebar')->required(),
                ColorPicker::make('sidebar_color_dark')->label('Sidebar (Dark)')->required(),
                ColorPicker::make('navtop_color')->label('Navtop')->required(),
                ColorPicker::make('navtop_color_dark')->label('Navtop (Dark)')->required(),
                ColorPicker::make('side_menu')->label('Side Menu Box')->required(),
                ColorPicker::make('side_menu_dark')->label('Side Menu Box (Dark)')->required(),
                ColorPicker::make('footer_color')->label('Footer Color')->required(),
                ColorPicker::make('footer_color_dark')->label('Footer Color (Dark)')->required(),
                
            ])->columns(4);
    }

    protected function getSectionCss(): Section
    {
        return Section::make('CSS QUE PRATICAMENTE NÃO MUDA')
            ->schema([
                ColorPicker::make('primary_color')->label('Primary Color')->required(),
                ColorPicker::make('primary_opacity_color')->label('Primary Opacity Color')->required(),
                ColorPicker::make('input_primary')->label('Input Primary')->required(),
                ColorPicker::make('input_primary_dark')->label('Input Primary (Dark)')->required(),
                ColorPicker::make('card_color')->label('Card Primary')->required(),
                ColorPicker::make('card_color_dark')->label('Card Primary (Dark)')->required(),
                ColorPicker::make('secundary_color')->label('Secundary Color')->required(),
                ColorPicker::make('gray_dark_color')->label('Gray Dark Color')->required(),
                ColorPicker::make('gray_light_color')->label('Gray Light Color')->required(),
                ColorPicker::make('gray_medium_color')->label('Gray Medium Color')->required(),
                ColorPicker::make('gray_over_color')->label('Gray Over Color')->required(),
                ColorPicker::make('title_color')->label('Title Color')->required(),
                ColorPicker::make('text_color')->label('Text Color')->required(),
                ColorPicker::make('sub_text_color')->label('Sub Text Color')->required(),
                ColorPicker::make('placeholder_color')->label('Placeholder Color')->required(),
                ColorPicker::make('background_color')->label('Background Color')->required(),
                TextInput::make('border_radius')->label('Border Radius')->required(),
            ])->columns(4);
    }

    protected function getSectmensagemhome(): Section
    {
        return Section::make('MENSAAGEM HOME')
            ->label('Altere a mensagem da Home')
            ->schema([
                TextInput::make("sobre_fotter")->label("Sobre do Footer"),
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
        return Section::make()
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
                Notification::make()->title('SISTEMA ATIVADO')->body('Dados alterados com sucesso!')->success()->send();
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
