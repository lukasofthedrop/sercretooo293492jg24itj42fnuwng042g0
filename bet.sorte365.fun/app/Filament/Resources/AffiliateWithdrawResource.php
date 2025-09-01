<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateWithdrawResource\Pages;
use App\Models\AffiliateWithdraw;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
class AffiliateWithdrawResource extends Resource
{
    protected static ?string $model = AffiliateWithdraw::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Apenas o admin tem acesso
    }
    
    public static function getNavigationLabel(): string
    {
        return 'SAQUES DE AFILIADOS'; // Rótulo exclusivo para o admin
    }
    
    public static function getModelLabel(): string
    {
        return 'SAQUES DE AFILIADOS'; // Rótulo exclusivo para o admin
    }
    

    public static function getGloballySearchableAttributes(): array
    {
        return ['pix_type', 'bank_info', 'user.name', 'user.email'];
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
            ->query(AffiliateWithdraw::query())
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->formatStateUsing(fn (AffiliateWithdraw $record): string => $record->symbol . ' ' . $record->amount)
                    ->sortable(),
                Tables\Columns\TextColumn::make('pix_type')
                    ->label('Tipo de Chave')
                    ->formatStateUsing(fn (string $state): string => \Helper::formatPixType($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('pix_key')
                    ->label('Chave Pix'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (AffiliateWithdraw $record): string => match($record->status) {
                        0 => 'Pendente',
                        1 => 'Aprovado',
                        2 => 'Cancelado',
                        default => 'Desconhecido'
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
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
                            ->when($data['created_from'], fn ($query) => $query->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn ($query) => $query->whereDate('created_at', '<=', $data['created_until']));
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
                    ->query(fn ($query, $data) => isset($data['status']) ? $query->where('status', $data['status']) : $query),
            ])
            ->actions([

                Action::make('delete')
                    ->label('Excluir')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (AffiliateWithdraw $withdrawal): bool => in_array($withdrawal->status, [0, 1, 2]))
                    ->action(function (AffiliateWithdraw $withdrawal) {
                        $withdrawal->delete();
                        \Filament\Notifications\Notification::make()
                            ->title('Saque Excluído')
                            ->success()
                            ->persistent()
                            ->body('O saque foi excluído com sucesso.')
                            ->send();
                    }),
                    Action::make('approve_payment')
                    ->label('Fazer pagamento')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(AffiliateWithdraw $record): bool => !$record->status)
                
                    // Cria um form com um campo de senha
                    ->form([
                        Forms\Components\TextInput::make('senha')
                            ->label('Digite a senha para concluir o saque')
                            ->password()
                            ->required(),
                    ])
                
                    // Exibe modal
                    ->requiresConfirmation()
                
                    ->modalHeading('Confirmação de saque')
                    ->modalButton('Solicitar Saque')
                
                    // Callback ao submeter o form do modal:
                    ->action(function (AffiliateWithdraw $record, array $data) {
                        // Verifica se preencheu a senha
                        if (! $data['senha']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Informe a senha')
                                ->danger()
                                ->body('Você não digitou a senha.')
                                ->send();
                
                            return;
                        }
                
                        // Monta a rota do Controller que valida e faz o saque.
                        // Agora passamos 'tipo=afiliado' para o Controller saber que é AffiliateWithdraw
                        $route = route('withdrawal', [
                            'id' => $record->id,
                            'tipo' => 'afiliado',
                        ]);
                
                        // Redireciona com a senha por GET
                        return redirect()->to($route . '&senha=' . urlencode($data['senha']));
                        // Se preferir, use ?senha= se não houver mais parâmetros.
                        // Se a rota já tiver ?id=..., troque por '&senha=...'
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateWithdraws::route('/'),
        ];
    }
}
