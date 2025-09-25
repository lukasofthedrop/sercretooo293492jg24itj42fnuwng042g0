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
                                "aureolink" => "AureoLink",
                                "slot1" => "Slot Vazio 1",
                                "slot2" => "Slot Vazio 2",
                                "slot3" => "Slot Vazio 3"
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
                            Toggle::make('aureolink_is_enable')
                                ->label('AureoLink')
                                ->default(true),
                            Toggle::make('slot1_is_enable')
                                ->label('Slot Vazio 1')
                                ->default(false)
                                ->disabled(),
                            Toggle::make('slot2_is_enable')
                                ->label('Slot Vazio 2')
                                ->default(false)
                                ->disabled(),
                            Toggle::make('slot3_is_enable')
                                ->label('Slot Vazio 3')
                                ->default(false)
                                ->disabled(),
                        ])->columns(2),
                ])->columns(2)
            ])
            ->statePath('data');
    }
}
