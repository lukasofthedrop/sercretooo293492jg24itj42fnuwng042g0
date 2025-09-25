<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\HtmlString;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Contracts\Support\Htmlable;

class DefaultSetting extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.default-setting';

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
        return __('AJUSTE OS DADOS DA PLATAFORMA'); 
    }

    public Setting $record;
    public ?array $data = [];

    /**
     * @dev  
     * @return void
     */
    public function mount(): void
    {   
        $envs = DotenvEditor::load(base_path('.env'));
        $setting = Setting::first();
        $this->record = $setting;
        $this->record->url_env = $envs->getValue("FILAMENT_BASE_URL");
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

            $favicon   = $this->data['software_favicon'];
            $logoWhite = $this->data['software_logo_white'];
            $logoBlack = $this->data['software_logo_black'];
            $softwareBackground = $this->data['software_background'];

            if (is_array($softwareBackground) || is_object($softwareBackground)) {
                if(!empty($softwareBackground)) {
                    $this->data['software_background'] = $this->uploadFile($softwareBackground);

                    if(is_array($this->data['software_background'])) {
                        unset($this->data['software_background']);
                    }
                }
            }

            if (is_array($favicon) || is_object($favicon)) {
                if(!empty($favicon)) {
                    $this->data['software_favicon'] = $this->uploadFile($favicon);

                    if(is_array($this->data['software_favicon'])) {
                        unset($this->data['software_favicon']);
                    }
                }
            }

            if (is_array($logoWhite) || is_object($logoWhite)) {
                if(!empty($logoWhite)) {
                    $this->data['software_logo_white'] = $this->uploadFile($logoWhite);

                    if(is_array($this->data['software_logo_white'])) {
                        unset($this->data['software_logo_white']);
                    }
                }
            }

            if (is_array($logoBlack) || is_object($logoBlack)) {
                if(!empty($logoBlack)) {
                    $this->data['software_logo_black'] = $this->uploadFile($logoBlack);

                    if(is_array($this->data['software_logo_black'])) {
                        unset($this->data['software_logo_black']);
                    }
                }
            }

            // CORREÇÃO: Controla output buffer para evitar crash da página
            if (ob_get_level()) ob_end_clean();
            ob_start();
            
            $envs = DotenvEditor::load(base_path('.env'));

            $envs->setKeys([
                'APP_NAME' => $this->data['software_name'],
                'FILAMENT_BASE_URL' => $this->data['url_env']
            ]);

            $envs->save();
            
            // Limpa qualquer output gerado pela modificação do .env
            if (ob_get_level()) ob_clean();

            if($setting->update($this->data)) {
                Cache::put('setting', $setting);

                Notification::make()
                    ->title('Dados alterados')
                    ->body('Dados alterados com sucesso!')
                    ->success()
                    ->send();

                redirect(route('filament.admin.resources.settings.index'));

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
                Section::make('ALTERE LOGOTIPO E DADOS')
                    ->description('Altere o logotipo e os dados da plataforma')
                    ->schema([
                        Group::make()->schema([
                            TextInput::make('software_name')
                                ->label('NOME DA PLATAFORMA')
                                ->placeholder('Digite o nome da plataforma')
                                ->required()
                                ->maxLength(191),
                            TextInput::make('software_description')
                                ->placeholder('Digite a descrição da plataforma')
                                ->label('DESCRICÃO DA PLATAFORMA')
                                ->maxLength(191),
                            TextInput::make('url_env')
                                ->label('URL DO PAINEL ADMIN')
                                ->placeholder('Digite a url que o painel admin vai ter')
                                ->required()
                                ->maxLength(191),
                        ])->columns(2),
                        Group::make()->schema([
                            FileUpload::make('software_favicon')
                                ->label('FIVICON | --> [ 52x52 ]')
                                ->placeholder('Carregue um favicon')
                                ->image(),
                            Group::make()->schema([
                                FileUpload::make('software_logo_white')
                                    ->label('LOGOTIPO 1  | --> [ 1228 x 274 ] FICA NA HOME')
                                    ->placeholder('Carregue uma logo branca')
                                    ->image()
                                    ->columnSpanFull(),
                                FileUpload::make('software_logo_black')
                                    ->label('LOGOTIPO 2  | --> [ 400x100 ] FICA NO CARREGAMENTO')
                                    ->placeholder('Carregue uma img')
                                    ->image()
                                    ->columnSpanFull()
                            ])
                        ])->columns(2),
                    ])
            ])
            ->statePath('data') ;
    }

    /**
     * @dev  
     * @param $array
     * @return mixed|void
     */
    private function uploadFile($array)
    {
        if (!is_array($array) && !is_object($array)) {
            // Retorna a string diretamente, pois não é um arquivo para upload
            return $array;
        }
    
        if(!empty($array)) {
            foreach ($array as $k => $temporaryFile) {
                if ($temporaryFile instanceof TemporaryUploadedFile) {
                    $path = \Helper::upload($temporaryFile);
                    if($path) {
                        return $path['path'];
                    }
                } else {
                    return $temporaryFile;
                }
            }
        }
    }
}
