<?php

namespace App\Filament\Pages;

use App\Models\ConfigPlayFiver;
use App\Models\GamesKey;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GamesKeyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.games-key-page';

    protected static ?string $title = 'CHAVES PLAYFIVER';

    protected static ?string $slug = 'chaves-dos-jogos';

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


    public ?array $data = [];
    public ?GamesKey $setting;

    /**
     * @return void
     */
    public function mount(): void
    {
        $gamesKey = GamesKey::first();
        $infos = $this->getInfo();
        $formData = [];
        if ($gamesKey) {
            $formData = array_merge($formData, [
                'playfiver_code'   => $gamesKey->playfiver_code,
                'playfiver_token'  => $gamesKey->playfiver_token,
                'playfiver_secret' => $gamesKey->playfiver_secret,
            ]);
        }
        if ($infos) {
            $formData = array_merge($formData, [
                'rtp'           => $infos->rtp,
                'limit_enable'  => $infos->limit_enable,
                'limit_amount'  => $infos->limit_amount,
                'limit_hours'   => $infos->limit_hours,
                'bonus_enable'  => $infos->bonus_enable,
            ]);
        }
        $this->form->fill($formData);
    }

/**
 * @param Form $form
 * @return Form
 */
public function form(Form $form): Form
{
    $data = ConfigPlayFiver::where("edit", true)->latest('id')->first(["edit", "updated_at"]);
    $locked = session()->get('agent_locked', false);
    $minutesPassed = 10;
    if ($data != null) {
        $minutesPassed = now()->diffInMinutes($data->updated_at);
        if ($minutesPassed < 10) {
            $locked = session()->get('agent_locked', true);
        }
    }

    return $form
        ->schema([
            Section::make('PLAYFIVER API')
                ->description(new \Illuminate\Support\HtmlString('
                    <div style="display: flex; align-items: center;">
                        Nossa API fornece diversos jogos de slots e ao vivo. :
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
                           href="https://playfiver.app" 
                           target="_blank">
                            PAINEL PLAYFIVER
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
                           href="https://t.me/playfivers" 
                           target="_blank">
                            GRUPO TELEGRAM
                        </a>
                    </div>
                    <b>AGENTE ATUAL: sorte365bet</b><br>
                    <b>AGENTE PREPARADO: lucrativabt (para lucrativa.bet)</b><br>
                    <b>Callback URL Atual: ' . url("/playfiver/webhook", [], true) . "</b>"))
                
                ->schema([
                    Section::make('CHAVES DE ACESSO PLAYFIVER')
                        ->description('Você pode obter suas chaves de acesso no painel da Playfiver ao criar o seu agente.')
                        ->schema([
                            TextInput::make('playfiver_code')
                                ->label('CÓDIGO DO AGENTE')
                                ->placeholder('Digite aqui o código do agente')
                                ->maxLength(191),
                            TextInput::make('playfiver_token')
                                ->label('AGENTE TOKEN')
                                ->placeholder('Digite aqui o token do agente')
                                ->maxLength(191),
                            TextInput::make('playfiver_secret')
                                ->label('AGENTE SECRETO')
                                ->placeholder('Digite aqui o código secreto do agente')
                                ->maxLength(191),
                        ])->columns(3),
                    Section::make('CONFIGURAÇÃO DO AGENTE')
                        ->description('Você pode configurar o RTP, os limites e os bônus nesta área. (As informações podem estar desatualizadas em relação às da própria PlayFiver.)')
                        ->schema([
                            TextInput::make('rtp')
                                ->label('RTP')
                                ->disabled(fn () => $locked),
                            TextInput::make('limit_amount')
                                ->label('Quantia de limite')
                                ->disabled(fn () => $locked),
                            TextInput::make('limit_hours')
                                ->label('Quantas horas vale o limite')
                                ->disabled(fn () => $locked),
                            Toggle::make('limit_enable')
                                ->label('Limite ativo')
                                ->disabled(fn () => $locked),
                            Toggle::make('bonus_enable')
                                ->label('Bônus ativo')
                                ->disabled(fn () => $locked),
                            Placeholder::make('')
                                ->extraAttributes(['class' => 'flex justify-end'])
                                ->disabled(fn () => $locked)
                                ->content(fn () => new \Illuminate\Support\HtmlString('
                                    <button 
                                        type="button"
                                        wire:click="saveInfo"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                        style="background-color: #f800ff; border-radius: 17px; width: 164px;text-align: center; cursor:pointer;">
                                        Atualizar Informações
                                    </button>
                                ')),
                            View::make('filament.forms.locked-agent')
                                ->viewData(["minutes" => 10 - $minutesPassed])
                                ->visible(fn() => $locked),
                        ])->columns(3)
                        ->extraAttributes(['class' => 'relative overflow-hidden min-h-[250px] bg-white/30 backdrop-blur-lg']),
                ]),
            // Nova seção para solicitar a senha de 2FA antes de salvar alterações
            Section::make('Confirmação de Alteração')
                ->schema([
                    TextInput::make('admin_password')
                        ->label('Senha de 2FA')
                        ->placeholder('Digite a senha de 2FA')
                        ->password()
                        ->required()
                        // Esse método faz com que o valor não seja persistido no model
                        ->dehydrateStateUsing(fn($state) => null),
                ]),
        ])
        ->statePath('data');
}


public function saveInfo() {
    try{
        $setting = GamesKey::first();

        $response = Http::withOptions([
            'force_ip_resolve' => 'v4', // Forçar IPv4
        ])->put('https://api.playfivers.com/api/v2/agent', [
            'agentToken' => $setting->playfiver_token,
            'secretKey'  => $setting->playfiver_secret,
            "rtp" => $this->data['rtp'],
            "limit_enable" => $this->data['limit_enable'],
            "limite_amount" => $this->data['limit_amount'],
            "limit_hours" => $this->data['limit_hours'],
            "bonus_enable" => $this->data['bonus_enable']
        ]);

        if($response->successful()){
            ConfigPlayFiver::latest('id')->update(["edit" => true]);
            return redirect("/admin/chaves-dos-jogos");
        }

        Notification::make()
            ->title('Atenção')
            ->body('Ocorreu um erro ao tentar atualizar os dados da playfiver')
            ->danger()
            ->send();
    }catch(Exception $e){
        Notification::make()
            ->title('Atenção')
            ->body('Ocorreu um erro ao tentar atualizar os dados da playfiver')
            ->danger()
            ->send();
    }
}

    private function getInfo()
    {
        try{
            $setting = GamesKey::first();
            $response = Http::withOptions([
                'force_ip_resolve' => 'v4', // Forçar IPv4
            ])->get('https://api.playfivers.com/api/v2/agent', [
                'agentToken' => $setting->playfiver_token,
                'secretKey'  => $setting->playfiver_secret,
            ]);
    
            if ($response->successful()) {
                $response = $response->json();
                $data = ConfigPlayFiver::create([
                    'rtp' => $response['data']['rtp'],
                    'limit_enable' => $response['data']['limit_enable'],
                    'limit_amount' => $response['data']['limit_amount'],
                    'limit_hours' => $response['data']['limit_hours'],
                    'bonus_enable' => $response['data']['bonus_enable'],
                ]);
                return $data; 
            } else {
                $data = ConfigPlayFiver::latest('id')->first();
                if($data == null){
                    throw new Exception();
                }
                return $data; 
            }
        } catch(Exception $e) {
            Log::error('Erro ao atualizar informações da PlayFiver:', ['exception' => $e->getMessage()]);
            Notification::make()
                ->title('Atenção')
                ->body('Ocorreu um erro ao tentar recuperar os dados da playfiver')
                ->danger()
                ->send();
            return null;
        }
    }
    
    /**
     * @return void
     */
    public function submit(): void
    {
        try {
            // Se a aplicação estiver em modo demo, bloqueia a alteração.
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Atenção')
                    ->body('Você não pode realizar esta alteração na versão demo')
                    ->danger()
                    ->send();
                return;
            }
    
            // Validação da senha de 2FA: Verifica se o campo 'admin_password' está presente
            // e se o valor informado bate com o token definido em TOKEN_DE_2FA.
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
    
            // Prossegue com a atualização ou criação dos dados
            $setting = GamesKey::first();
            if (!empty($setting)) {
                if ($setting->update($this->data)) {
                    Notification::make()
                        ->title('SISTEMA ATIVADO')
                        ->body('Suas chaves foram alteradas com sucesso!')
                        ->success()
                        ->send();
                }
            } else {
                if (GamesKey::create($this->data)) {
                    Notification::make()
                        ->title('SISTEMA ATIVADO')
                        ->body('Suas chaves foram criadas com sucesso!')
                        ->success()
                        ->send();
                }
            }
        } catch (Halt $exception) {
            Notification::make()
                ->title('Erro ao alterar dados!')
                ->body('Erro ao alterar dados!')
                ->danger()
                ->send();
        }
    }
    
}
