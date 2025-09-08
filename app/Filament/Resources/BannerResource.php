<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\HtmlString;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Banner';
    protected static ?string $pluralLabel = 'ADICIONAR BANNERS NA PÁGINA INICIAL';

    protected static ?string $navigationLabel = 'DEFINIÇÕES DE BANNERS';

    protected static ?string $navigationGroup = 'Marketing';  // Opcional: para agrupar em uma seção de navegação

    /**
     * @dev Sistema WhiteLabel Casino - Profissional
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('CONFIGURAÇÃO DE BANNERS PROMOCIONAIS')
                    ->description('Configure os banners promocionais da plataforma'),
                Forms\Components\Section::make()
                    ->description('')
                    ->schema([
                        Forms\Components\Group::make()->schema([
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\TextInput::make('link')
                                        ->label('Link')
                                        ->placeholder('Digite o link do banner')
                                        ->maxLength(191),
                                    Forms\Components\Select::make('type')
                                        ->label('Selecione o tipo')
                                        ->options([
                                            'carousel' => 'Banner na Carousel',
                                            'home' => 'Banner na Home',
                                        ])
                                        ->required(),
                                ])->columns(2)->columnSpanFull(),

                            Forms\Components\Textarea::make('description')
                                ->placeholder('Digite uma descrição')
                                ->maxLength(65535)
                                ->columnSpanFull(),
                        ])->columns(2),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->required()
                            ->directory('/')
                            ->disk('public')
                            ->visibility('public'),
                    ])
            ]);
    }

    /**
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image')
                    ->label('Imagem')
                    ->formatStateUsing(function ($state) {
                        $url = '/storage/' . $state;
                        return new HtmlString('<img src="' . $url . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">');
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('link')
                    ->label('Link'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
