<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'CATEGORIAS DE JOGOS';

    protected static ?string $modelLabel = 'Todas as Categorias';

    /**
     * @dev  
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Define o formulário para criação/edição de categorias.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('NOME DA CATEGORIA')
                            ->required()
                            ->unique(ignorable: fn ($record) => $record)
                            ->maxLength(191),
                        Forms\Components\TextInput::make('description')
                            ->label('DESCRIÇÃO DA CATEGORIA')
                            ->required()
                            ->maxLength(191),
                        Forms\Components\FileUpload::make('image')
                            ->label('IMAGEM DA CATEGORIA')
                            ->image(),
                        Forms\Components\TextInput::make('slug')
                            ->label('NOME EXIBIDO NA HOME')
                            ->maxLength(191),
                        Forms\Components\TextInput::make('url')
                            ->label('LINK OPCIONAL')
                            ->placeholder('https://exemplo.com')
                            ->url()
                            ->maxLength(191),
                    ])->columns(2)
            ]);
    }

    /**
     * Define as colunas da tabela de categorias.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('NOME DA CATEGORIA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('image')
                    ->label('IMAGEM DA CATEGORIA')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '';
                        $url = '/storage/' . $state;
                        return new HtmlString('<img src="' . $url . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">');
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('url')
                    ->label('LINK OPCIONAL')
                    ->url(fn ($record) => $record->url ?? '#') // Adiciona link ou "#" se estiver vazio
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Define as relações disponíveis no recurso.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Define as páginas disponíveis no recurso.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'), // Ativada
            'edit' => Pages\EditCategory::route('/{record}/edit'), // Ativada
        ];
    }
}
