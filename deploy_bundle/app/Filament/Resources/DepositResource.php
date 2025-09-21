<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages;
use App\Models\Deposit;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'GESTÃO DE DEPÓSITOS';

    protected static ?string $modelLabel = 'GESTÃO DE DEPÓSITOS';

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $slug = 'todos-depositos';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 0)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 0)->count() > 5 ? 'success' : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cadastro de Depósito')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuários')
                            ->placeholder('Selecione um usuário')
                            ->options(
                                fn($get) => User::query()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Valor')
                            ->required()
                            ->default(0.00),
                        Forms\Components\FileUpload::make('proof')
                            ->label('Comprovante')
                            ->placeholder('Carregue a imagem do comprovante')
                            ->image()
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\Toggle::make('status')
                            ->label('Pago')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('USUÁRIO')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('ID DO PAGAMENTO')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('VALOR')
                    ->formatStateUsing(fn(Deposit $record): string => 'R$ ' . number_format($record->amount, 2, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('STATUS')
                    ->formatStateUsing(fn(Deposit $record): string => $record->status ? 'Pago' : 'Não Pago')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('DATA DE CRIAÇÃO')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('DATA DE ATUALIZAÇÃO')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('data')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
                Filter::make('status')
                    ->label('Status')
                    ->default(null) // Define "Todos" como padrão
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                '' => 'Todos',
                                '1' => 'Pago',
                                '0' => 'Não Pago',
                            ])
                            ->default(''), // Define "Todos" como padrão
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['status']) && $data['status'] !== '') {
                            $query->where('status', $data['status']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mudarParaPago')
                    ->label('Mudar para Pago')
                    ->action(function (Deposit $record) {
                        $record->update(['status' => 1]);
                    })
                    ->icon('heroicon-o-check')
                    ->visible(fn(Deposit $record) => $record->status == 0), // Apenas mostrar para "Não Pago"
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(false) // Remover confirmação de exclusão
                    ->modalHeading(fn() => null) // Remover o cabeçalho do modal
                    ->modalButton(fn() => null)  // Remover o botão do modal
                    ->modalDescription(fn() => null), // Remover a descrição do modal
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(false) // Remover confirmação de exclusão em massa
                        ->modalHeading(fn() => null) // Remover o cabeçalho do modal
                        ->modalButton(fn() => null)  // Remover o botão do modal
                        ->modalDescription(fn() => null), // Remover a descrição do modal
                ]),
            ])
            ->emptyStateActions([
                // Ações do estado vazio
            ]);
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
            'index' => Pages\ListDeposits::route('/'),
        ];
    }
}
