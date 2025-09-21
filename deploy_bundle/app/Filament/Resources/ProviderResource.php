<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Helpers\Core as Helper;
use Illuminate\Database\Eloquent\Model;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'PROVEDORES DE JOGOS';

    protected static ?string $modelLabel = 'Todos os Provedores';

    /**
     * Garante que apenas usuários com o papel de admin possam acessar.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Define o formulário para criar/editar provedores.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('CRIE OU EDITE UM PROVEDOR')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('CODIGO DO PROVEDOR')
                            ->placeholder('Digite o Código')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Digite o Nome')
                            ->label('NOME DO PROVEDOR')
                            ->maxLength(50),
                        Forms\Components\Toggle::make('status')
                            ->label('STATUS DO PROVEDOR')
                            ->inline(false)
                            ->required(),
                    ])
                    ->columns(3)
            ]);
    }

    /**
     * Define a tabela para listar provedores.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('CÓDIGO DO PROVEDOR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('NOME DO PROVEDOR')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('STATUS DO PROVEDOR')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Defina aqui quaisquer relações que precisar exibir na interface do Filament.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            // Se precisar, ative as rotas de criação e edição:
            // 'create' => Pages\CreateProvider::route('/create'),
            // 'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}

