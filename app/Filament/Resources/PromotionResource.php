<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promocao;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;




class PromotionResource extends Resource
{
    protected static ?string $model = Promocao::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Promoção';
    protected static ?string $pluralLabel = 'Promoções';
    protected static ?string $navigationLabel = 'Gerenciar Promoções';
    protected static ?string $navigationGroup = 'Marketing';

    /**
     * Controla o acesso ao recurso
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Define o formulário para criação/edição
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('link')
                    ->label('Link')
                    ->placeholder('Digite o link da promoção')
                    ->url()
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('imagem')
                    ->label('Imagem')
                    ->image()
                    ->required()
                    ->directory('/uploads/promocoes') // Diretório onde as imagens serão salvas
                    ->placeholder('Carregue uma imagem'),
                Forms\Components\RichEditor::make('regras_html')
                    ->label('Regras')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'bulletList',
                        'orderedList',
                        'link',
                        'codeBlock',
                    ]),

            ]);
    }

    /**
     * Configura a tabela de exibição no painel
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('imagem')
                    ->label('Imagem')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '';
                        // Imagens de promoções já incluem o caminho completo
                        $url = str_starts_with($state, 'uploads/') ? '/storage/' . $state : '/storage/' . $state;
                        return new HtmlString('<img src="' . $url . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">');
                    })
                    ->html(),
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link')
                    ->label('Link')
                    ->url(fn ($record) => $record->link, true)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                // Adicione filtros personalizados se necessário
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    /**
     * Configura as páginas do recurso
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
