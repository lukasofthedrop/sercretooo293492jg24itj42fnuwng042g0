<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class ChangePasswordUser extends Page implements HasForms
{
    use HasPageSidebar, InteractsWithForms;

    public User $record;
    public ?array $data = [];

    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.user-resource.pages.change-password-user';
    protected static ?string $title = 'Alterar Senha';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function save()
    {
        // 1. Verifica se a senha extra (admin_password) informada confere com a definida no .env
        if (!isset($this->data['admin_password']) || $this->data['admin_password'] !== env('TOKEN_DE_2FA')) {
            Notification::make()
                ->title('Senha incorreta')
                ->body('A senha de confirmação não confere.')
                ->danger()
                ->send();
            // Interrompe a execução sem salvar
            return;
        }

        try {
            $user = User::find($this->record->id);
            // Atualiza somente se as informações já foram validadas (assumindo que 'confirm_password' está validada no form)
            $user->update(['password' => $this->data['password']]);

            Notification::make()
                ->title('Senha Alterada')
                ->body('A senha foi alterada com sucesso! O usuário precisará relogar.')
                ->success()
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('TROQUE A SENHA DO USUÁRIO')
                ->description('Após alterar a senha, o usuário será desconectado e deverá fazer login novamente.')
                ->schema([
                    TextInput::make('password')
                        ->label('SENHA DO USUÁRIO')
                        ->placeholder('Digite a nova senha')
                        ->password()
                        ->required()
                        ->maxLength(191),
                    TextInput::make('confirm_password')
                        ->label('REPITA A SENHA')
                        ->placeholder('Confirme a nova senha')
                        ->password()
                        ->required()
                        ->confirmed() // Garante que o valor seja igual ao campo "password"
                        ->maxLength(191),
                    // Novo campo para a senha extra do admin
                    TextInput::make('admin_password')
                        ->label('TOKEN DE 2FA')
                        ->placeholder('Digite a senha de 2fa de confirmação')
                        ->password()
                        ->required()
                        ->maxLength(191),
                ])
                ->columns(2)
                ->statePath('data'),
        ];
    }
}
