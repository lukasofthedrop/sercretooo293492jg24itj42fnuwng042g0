<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;
    protected static string $view = 'filament.resources.setting-resource.pages.payment-setting';

    public Setting $record;
    public ?array $data = [];

    public function getTitle(): string | Htmlable
    {
        return __('AREA FINANCEIRA');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function mount(): void
    {
        $setting = Setting::first();
        $this->record = $setting;
        $this->form->fill($setting->toArray());
    }

    public function save()
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

            $setting = Setting::find($this->record->id);

            if ($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('Dados alterados')
                    ->body('Dados alterados com sucesso!')
                    ->success()
                    ->send();

                return redirect(route('filament.admin.resources.settings.payment', ['record' => $this->record->id]));
            }
        } catch (Halt $exception) {
            return;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('VSALATIEL.COM CRIOU ESSA PLATAFORMA PARA VOCÊ')
                ->description(new HtmlString('
                    <div style="font-weight: 600; display: flex; align-items: center;">
                        SAIBA MAIS SOBRE NÓS. PARTICIPE DA NOSSA COMUNIDADE IGAMING. ACESSE AGORA! 
                        <a class="dark:text-white" 
                           style="
                                font-size: 14px;
                                font-weight: 600;
                                width: 127px;
                                display: flex;
                                background-color: #f800ff;
                                padding: 10px;
                                border-radius: 11px;
                                justify-content: center;
                                margin-left: 10px;
                           " 
                           href="https://vsalatiel.com" 
                           target="_blank">
                            SITE OFICIAL
                        </a>
                        <a class="dark:text-white" 
                           style="
                                font-size: 14px;
                                font-weight: 600;
                                width: 127px;
                                display: flex;
                                background-color: #f800ff;
                                padding: 10px;
                                border-radius: 11px;
                                justify-content: center;
                                margin-left: 10px;
                           " 
                           href="https://t.me/victormsalatiel" 
                           target="_blank">
                            GRUPO TELEGRAM
                        </a>
                    </div>
                ')),

                Section::make('AJUSTE DE COMISSÃO CPA')
                ->description('Ajuste o valor da comissão CPA e depósito mínimo para o afiliado ganhar o CPA.')
                ->schema([
                    TextInput::make('cpa_baseline')
                        ->label('DEPÓSITO MÍNIMO CPA')
                        ->helperText('Valor mínimo que o usuário deve depositar para o afiliado ganhar o CPA.')
                        ->numeric()
                        ->suffix('R$ ')
                        ->maxLength(191),
                    TextInput::make('cpa_value')
                        ->label('AFILIADO CPA')
                        ->helperText('Valor da comissão CPA que o afiliado ganhará.')
                        ->numeric()
                        ->suffix('R$')
                        ->maxLength(191)
                ])->columns(2),

                Section::make('AJUSTE AS CONFIGURAÇÕES DE PAGAMENTO')
                    ->description('Você pode ajustar plataforma de saque, depósito e limites')
                    ->schema([
                        Select::make("saque")
                            ->label("RESPONSAVEL PELO SISTEMA DE SAQUE")
                            ->options([
                                "ezzepay" => "EzzePay",
                                "suitpay" => "SuitPay",
                                "digitopay" => "Digito Pay",
                                "bspay" => "BsPay"
                                
                            ]),
                        TextInput::make('min_deposit')
                            ->label('DEPÓSITO MÍNIMO')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('max_deposit')
                            ->label('DEPÓSITO MÁXIMO')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('min_withdrawal')
                            ->label('SAQUE MÍNIMO')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('max_withdrawal')
                            ->label('SAQUE MÁXIMO')
                            ->numeric()
                            ->maxLength(191),
                        TextInput::make('initial_bonus')
                            ->label('PORCENTAGEM DE BÔNUS')
                            ->numeric()
                            ->suffix('%')
                            ->maxLength(191),
                    Section::make('GATEWAYS DE PAGAMENTO')
                        ->description('Ative ou desative os gateways de sua preferência.')
                        ->schema([
                            Toggle::make('ezzepay_is_enable')
                                ->label('EzzePay'),
                            Toggle::make('digito_is_enable')
                                ->label('DigitoPay'),
                            Toggle::make('bspay_is_enable')
                                ->label('bspay'),
                            Toggle::make('suitpay_is_enable')
                                ->label('SuitPay'),
                        ])->columns(3),
                ])->columns(2)
            ])
            ->statePath('data');
    }
}
