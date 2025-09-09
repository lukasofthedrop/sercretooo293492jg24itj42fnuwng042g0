<?php

namespace App\Filament\Pages;

use App\Models\Gateway;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
// use Jackiedo\DotenvEditor\Facades\DotenvEditor; // REMOVIDO POR SEGURANÇA

class GatewayPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.gateway-page';

    public ?array $data = [];
    public Gateway $setting;

    /**
     * @dev  
     * @return bool 
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla o acesso total à página
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla a visualização de elementos específicos
    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $gateway = Gateway::first();
        if(!empty($gateway)) {
            $this->setting = $gateway;
            $this->form->fill($this->setting->toArray());
        }else{
            $this->form->fill();
        }
    }

/**
 * @param Form $form
 * @return Form
 */
public function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('REGISTRE SUAS CHAVES DE API GATEWAY')
            ->description('Configure suas chaves de API para os gateways de pagamento')
                ->schema([
                    Section::make('AUREOLINK')
                        ->description(new HtmlString('
                                    <div style="display: flex; align-items: center;">
                                        Gateway de pagamento AureoLink - Único gateway ativo no sistema:
                                        <a class="dark:text-white" 
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
                                        href="#" 
                                        target="_blank">
                                            Dashboard
                                        </a>
                                    </div>'))
                        ->schema([
                            TextInput::make('aureolink_uri')
                                ->label('CLIENTE URL')
                                ->placeholder('Digite a url da api AureoLink')
                                ->maxLength(191)
                                ->columnSpanFull(),
                            TextInput::make('aureolink_client')
                                ->label('CLIENTE ID')
                                ->placeholder('Digite o client ID AureoLink')
                                ->maxLength(191)
                                ->columnSpanFull(),
                            TextInput::make('aureolink_secret')
                                ->label('CLIENTE SECRETO')
                                ->placeholder('Digite o client secret AureoLink')
                                ->maxLength(191)
                                ->columnSpanFull(),
                        ]),
                    
                    Section::make('SLOT VAZIO 1')
                        ->description('Slot disponível para futuro gateway de pagamento')
                        ->schema([
                            TextInput::make('slot1_uri')
                                ->label('CLIENTE URL')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                            TextInput::make('slot1_client')
                                ->label('CLIENTE ID')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                            TextInput::make('slot1_secret')
                                ->label('CLIENTE SECRETO')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                        ]),
                    
                    Section::make('SLOT VAZIO 2')
                        ->description('Slot disponível para futuro gateway de pagamento')
                        ->schema([
                            TextInput::make('slot2_uri')
                                ->label('CLIENTE URL')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                            TextInput::make('slot2_client')
                                ->label('CLIENTE ID')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                            TextInput::make('slot2_secret')
                                ->label('CLIENTE SECRETO')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                        ]),
                    
                    Section::make('SLOT VAZIO 3')
                        ->description('Slot disponível para futuro gateway de pagamento')
                        ->schema([
                            TextInput::make('slot3_uri')
                                ->label('CLIENTE URL')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                            TextInput::make('slot3_client')
                                ->label('CLIENTE ID')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                            TextInput::make('slot3_secret')
                                ->label('CLIENTE SECRETO')
                                ->placeholder('Slot vazio - aguardando configuração')
                                ->maxLength(191)
                                ->disabled()
                                ->columnSpanFull(),
                        ]),
                    // Adicione esta seção dentro do array passado para ->schema([ ... ])
                    Section::make('Confirmação de Alteração')
                        ->schema([
                            TextInput::make('admin_password')
                                ->label('Senha de 2FA')
                                ->placeholder('Digite a senha de 2FA')
                                ->password()
                                ->required()
                                ->dehydrateStateUsing(fn($state) => null), // Para que o valor não seja persistido
                        ]),

                ]),
        ])
        ->statePath('data');
}


    /**
     * @return void
     */
/**
 * @return void
 */
public function submit(): void
{
    try {
        if (env('APP_DEMO')) {
            Notification::make()
                ->title('Atenção')
                ->body('Você não pode realizar esta alteração na versão demo')
                ->danger()
                ->send();
            return;
        }

        // Validação da senha de 2FA: verifica se 'admin_password' existe e bate com o TOKEN_DE_2FA
        if (
            !isset($this->data['admin_password']) ||
            $this->data['admin_password'] !== env('TOKEN_DE_2FA')
        ) {
            Notification::make()
                ->title('Acesso Negado')
                ->body('A senha de 2FA está incorreta. Você não pode atualizar os dados.')
                ->danger()
                ->send();
            return;
        }

        $setting = Gateway::first();
        if (!empty($setting)) {
            if ($setting->update($this->data)) {
                if (!empty($this->data['stripe_public_key'])) {
                    $envs = DotenvEditor::load(base_path('.env'));

                    $envs->setKeys([
                        'STRIPE_KEY' => $this->data['stripe_public_key'],
                        'STRIPE_SECRET' => $this->data['stripe_secret_key'],
                        'STRIPE_WEBHOOK_SECRET' => $this->data['stripe_webhook_key'],
                    ]);

                    $envs->save();
                }

                Notification::make()
                    ->title('SISTEMA ATIVADO')
                    ->body('Suas chaves foram alteradas com sucesso!')
                    ->success()
                    ->send();
            }
        } else {
            if (Gateway::create($this->data)) {
                Notification::make()
                    ->title('SISTEMA ATIVADO')
                    ->body('Suas chaves foram criadas com sucesso!')
                    ->success()
                    ->send();
            }
        }
    } catch (\Filament\Support\Exceptions\Halt $exception) {
        Notification::make()
            ->title('Erro ao alterar dados!')
            ->body('Erro ao alterar dados!')
            ->danger()
            ->send();
    }
}

}
