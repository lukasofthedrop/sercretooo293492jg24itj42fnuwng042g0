<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Illuminate\Support\HtmlString;
use App\Models\VsalatielKey;

class VsalatielKeyPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'VSALATIEL API';
    protected static ?string $slug = 'vsalatiel';
    protected static string $view = 'filament.pages.vsalatiel-key-page';

    public ?array $data = [];
    public ?VsalatielKey $editingKey = null;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function getKeysProperty()
    {
        return VsalatielKey::all();
    }

    public function edit($id)
    {
        $this->editingKey = VsalatielKey::findOrFail($id);
        $this->form->fill($this->editingKey->toArray());
    }

    public function delete($id)
    {
        VsalatielKey::findOrFail($id)->delete();
        $this->notify('success', 'Chave removida com sucesso!');
    }

    public function submit()
    {
        if ($this->editingKey) {
            $this->editingKey->update($this->form->getState());
            $this->notify('success', 'Chave atualizada com sucesso!');
            $this->editingKey = null;
        } else {
            VsalatielKey::create($this->form->getState());
            $this->notify('success', 'Chave cadastrada com sucesso!');
        }
        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('VSALATIEL API')
                    ->description(new HtmlString('
                        <div style="display: flex; align-items: center;">
                            Integração com a API VSALATIEL.COM.
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
                               href="https://api.vsalatiel.com"
                               target="_blank">
                                API VSALATIEL.COM
                            </a>
                        </div>
                        <b>Configure abaixo as credenciais da sua integração com a VSALATIEL.</b>
                    '))
                    ->schema([
                        Section::make('CHAVES DE ACESSO VSALATIEL')
                            ->description('Preencha os dados fornecidos pela VSALATIEL para ativar a integração.')
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
                                    ->placeholder('Ex: https://api.vsalatiel.com')
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
