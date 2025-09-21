<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\User;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

use Filament\Notifications\Notification;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'SAQUES DE USUÁRIOS';

    protected static ?string $modelLabel = 'SAQUE';

    protected static ?string $navigationGroup = 'GESTÃO E FINANÇAS';

    protected static ?string $slug = 'todos-saques';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['type', 'bank_info', 'user.name', 'user.last_name', 'user.cpf', 'user.phone',  'user.email'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 0)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 0)->count() > 5 ? 'success' : 'warning';
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('USUÁRIO')
                    ->searchable(['users.name', 'users.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('VALOR')
                    ->formatStateUsing(fn(Withdrawal $record): string => 'R$ ' . number_format($record->amount, 2, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('pix_type')
                    ->label('TIPO DE CHAVE')
                    ->formatStateUsing(fn(string $state): string => \Helper::formatPixType($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('pix_key')
                    ->label('CHAVE PIX'),
                Tables\Columns\TextColumn::make('status')
                    ->label('STATUS')
                    ->formatStateUsing(fn(Withdrawal $record): string => match ($record->status) {
                        0 => 'Pendente',
                        1 => 'Aprovado',
                        2 => 'Cancelado',
                        default => 'Desconhecido'
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('CRIADO EM')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('ATUALIZADO EM')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('Data de Criação')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Até'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($query) => $query->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($query) => $query->whereDate('created_at', '<=', $data['created_until']));
                    }),
                Filter::make('status')
                    ->label('Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                0 => 'Pendente',
                                1 => 'Aprovado',
                                2 => 'Cancelado',
                            ])
                            ->placeholder('Selecione um status'),
                    ])
                    ->query(fn($query, $data) => isset($data['status']) ? $query->where('status', $data['status']) : $query),
            ])
            ->actions([
                Action::make('refund_payment')
                    ->label('Reembolsar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Withdrawal $withdrawal): bool => !$withdrawal->status)
                    ->action(function (Withdrawal $withdrawal) {
                        $route = route('suitpay.cancelwithdrawal', ['id' => $withdrawal->id]);
                        \Filament\Notifications\Notification::make()
                            ->title('Reembolsar Saque')
                            ->success()
                            ->persistent()
                            ->body('Você está reembolsando o saque de ' . \Helper::amountFormatDecimal($withdrawal->amount))
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Confirmar')
                                    ->button()
                                     ->extraAttributes([
                                        'onclick' => <<<JS
                                            var apertou = false;
                                            this.disabled = true;
                                            if (!apertou) {
                                                apertou = true;
                                                var url = `$route`;
                                                window.location.href = url;
                                            }
                                        JS,
                                    ])     
                                    ->close(),
                                \Filament\Notifications\Actions\Action::make('undo')
                                    ->color('gray')
                                    ->label('Cancelar')
                                    ->action(function (Withdrawal $withdrawal) {
                                        // Ação de cancelamento se necessário
                                    })
                                    ->close(),
                            ])
                            ->send();
                    }),

                    
                    Action::make('approve_payment')
                        ->label('Fazer pagamento')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Withdrawal $record): bool => !$record->status)
                    
                        // 1) Cria um form para pedir a senha no modal
                        ->form([
                            Forms\Components\TextInput::make('senha')
                                ->label('Digite a senha de 2fa para concluir o saque')
                                ->password() // campo tipo password
                                ->required(),
                        ])
                    
                        // 2) Gera o modal ao clicar
                        ->requiresConfirmation()
                    
                        // 3) Personaliza título do modal e texto do botão
                        ->modalHeading('Confirmação de saque')
                        ->modalButton('Solicitar Saque')
                    
                        // 4) Callback que roda ao submeter o form do modal
                        ->action(function (Withdrawal $record, array $data) {
                            // Se quiser checar se digitou algo (opcional, pois '->required()' já força no Filament)
                            if (! $data['senha']) {
                                Notification::make()
                                    ->title('Informe a senha')
                                    ->danger()
                                    ->body('Você não digitou a senha.')
                                    ->send();
                    
                                return;
                            }
                    
                            // Se chegou até aqui, já passou pela required, mas vamos redirecionar
                            // para o Controller que valida a senha e faz o saque de verdade.
                    
                            // 1) Monta a rota do seu método "withdrawalFromModal(...)"
                            $route = route('withdrawal', ['id' => $record->id]); 
                            // Ex: /withdrawal/123
                    
                            // 2) Redireciona com a senha como query param
                            // No Filament v2, basta dar return redirect(...) 
                            // use urlencode() caso contenha caracteres especiais
                            return redirect()->to($route . '?senha=' . urlencode($data['senha']));
                        }),
                    
                
                Action::make('delete')
                    ->label('Excluir')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn(Withdrawal $withdrawal): bool => in_array($withdrawal->status, [0, 1, 2]))
                    ->action(function (Withdrawal $withdrawal) {
                        $withdrawal->delete();
                        \Filament\Notifications\Notification::make()
                            ->title('Saque Excluído')
                            ->success()
                            ->persistent()
                            ->body('O saque foi excluído com sucesso.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
        ];
    }
}
