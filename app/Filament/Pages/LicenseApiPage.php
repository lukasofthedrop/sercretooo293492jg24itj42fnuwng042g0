<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Illuminate\Support\HtmlString;
use App\Models\LicenseKey;
use Filament\Notifications\Notification;

class LicenseApiPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'LICENSE API';
    protected static ?string $slug = 'license-api';
    protected static string $view = 'filament.pages.license-api-page';

    public ?array $data = [];
    public ?LicenseKey $editingKey = null;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function getKeysProperty()
    {
        return LicenseKey::all();
    }

    public function edit($id)
    {
        $this->editingKey = LicenseKey::findOrFail($id);
        $this->form->fill($this->editingKey->toArray());
    }

    public function delete($id)
    {
        LicenseKey::findOrFail($id)->delete();
        Notification::make()
            ->title('Chave removida com sucesso!')
            ->success()
            ->send();
    }

    public function submit()
    {
        if ($this->editingKey) {
            $this->editingKey->update($this->form->getState());
            Notification::make()
                ->title('Chave atualizada com sucesso!')
                ->success()
                ->send();
            $this->editingKey = null;
        } else {
            LicenseKey::create($this->form->getState());
            Notification::make()
                ->title('Chave cadastrada com sucesso!')
                ->success()
                ->send();
        }
        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('LICENSE API')
                    ->description(new HtmlString('
                        <div style="display: flex; align-items: center;">
                            Integração com a API de licenciamento.
                            <a class="dark:text-white"
                               style="
                                    font-size: 14px;
                                    font-weight: 600;
                                    width: 180px;
                                    display: flex;
                                    background-color: #f800ff;
                                    padding: 10px;
                                    border-radius: 11px;
                                    justify-content: center;
                                    margin-left: 10px;
                               "
                               href="#"
                               target="_blank">
                                DOCUMENTAÇÃO API
                            </a>
                        </div>
                        <b>Configure abaixo as credenciais da sua integração de licenciamento.</b>
                    '))
                    ->schema([
                        Section::make('CHAVES DE ACESSO DA LICENÇA')
                            ->description('Preencha os dados de licenciamento para ativar a integração.')
                            ->schema([
                                TextInput::make('token')
                                    ->label('Token')
                                    ->placeholder('Digite o token da API')
                                    ->maxLength(191)
                                    ->required(),
                                TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->placeholder('Digite o Client ID')
                                    ->maxLength(191)
                                    ->required(),
                                TextInput::make('endpoint_base')
                                    ->label('Endpoint Base')
                                    ->placeholder('Ex: https://api.exemplo.com')
                                    ->maxLength(191)
                                    ->required(),
                                TextInput::make('timeout')
                                    ->label('Timeout (segundos)')
                                    ->placeholder('Ex: 30')
                                    ->numeric()
                                    ->required(),
                            ])->columns(2),
                    ]),
            ])
            ->statePath('data');
    }
}
