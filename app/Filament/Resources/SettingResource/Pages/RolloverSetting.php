<?php

namespace App\Filament\Resources\SettingResource\Pages;
use Illuminate\Support\HtmlString;
use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RolloverSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.rollover-setting';

    /**
     * @dev  
     * @param Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string | Htmlable
    {
        return __('SISTEMA DE ROLL-OVER');
    }

    public Setting $record;
    public ?array $data = [];

    /**
     * @dev  
     * @return void
     */
    public function mount(): void
    {
        $setting = Setting::first();
        $this->record = $setting;
        $this->form->fill($setting->toArray());
    }

    /**
     * @return void
     */
    public function save()
    {
        try {
            if(env('APP_DEMO')) {
                Notification::make()
                    ->title('Atenção')
                    ->body('Você não pode realizar está alteração na versão demo')
                    ->danger()
                    ->send();
                return;
            }
 
            $setting = Setting::find($this->record->id);

            if($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('ACESSE VSALATIEL.COM')
                    ->body('Aletarções realizadas com sucesso!')
                    ->success()
                    ->send();

            }
        } catch (Halt $exception) {
            return;
        }
    }

    /**
     * @dev  
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('CONFIGURAÇÕES DE ROLLOVER')
                    ->description('Configure o sistema de rollover da plataforma'),
                Section::make('SISTEMA DE PROTEÇÃO DE BÔNUS E DEPÓSITO')
                    ->description('Proteção para evitar saques sem apostas e lavagem de dinheiro.')
                    ->schema([
                        TextInput::make('rollover_deposit')
                            ->label('ROLL-OVER DEPÓSITO')
                            ->numeric()
                            ->default(1)
                            ->suffix('x')
                            ->helperText('A quantidade de multiplicação do depósito --> recomendado = 2')
                            ->maxLength(191),
                        
                        Group::make()->schema([ // Adiciona o group para rollover e rollover_protection
                            TextInput::make('rollover')
                                ->label('ROLL-OVER BÔNUS')
                                ->numeric()
                                ->default(1)
                                ->suffix('x')
                                ->helperText('a quantidade de multiplicação do Bônus --> recomendado = 5')
                                ->maxLength(191),
                            TextInput::make('rollover_protection')
                                ->label('Proteção de Rollover para Bônus')
                                ->numeric()
                                ->default(1)
                                ->suffix('x')
                                ->helperText('Defina a quantidade de transações mínimas para zerar o Rollover')
                                ->maxLength(191),
                        ])->columns(2),
    
                        Toggle::make('disable_rollover')
                            ->label('Desativar Rollover')
                            ->helperText('Se tiver desmarcado é porque está ativo o Rollover de Bônus e Depósito')
                    ])->columns(2),
            ])
            ->statePath('data');
    }
    
}
