<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameOpenConfigResource\Pages;
use App\Models\GameOpenConfig;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GameOpenConfigResource extends Resource
{
    protected static ?string $model = GameOpenConfig::class;

    protected static ?string $navigationIcon   = 'heroicon-o-lock-closed';
    protected static ?string $label            = 'Abertura de Jogos';
    protected static ?string $pluralLabel      = 'Abertura de Jogos';
    protected static ?string $navigationLabel  = 'Config. Abertura de Jogos';
    protected static ?string $navigationGroup  = 'Finanças';

    // Só admin acessa
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    // Define o form
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Toggle::make('requires_deposit_today')
                ->label('Exigir Depósito Diário?')
                ->helperText('Se ativo, o usuário só poderá abrir jogos se tiver depositado hoje.')
                ->default(false),
        ]);
    }

    // Tabela (geralmente não usada se for único registro, mas deixamos mínima)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('requires_deposit_today')
                    ->label('Exigir Depósito Diário')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultPagination(1);
    }

    // Força a query a pegar só 1 registro
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->limit(1);
    }

    // Páginas
    public static function getPages(): array
    {
        return [
            'index' => Pages\EditGameOpenConfig::route('/'),
        ];
    }
}
