<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VipResource\Pages;
use App\Models\Vip;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class VipResource extends Resource
{
    protected static ?string $model = Vip::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Níveis VIP';

    protected static ?string $pluralLabel = 'VIPs';

    protected static ?string $navigationGroup = 'Promoções'; // Agrupado na seção de Promoções

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Título do VIP')
                    ->required(),
                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3),
                TextInput::make('required_missions')
                    ->label('Missões Necessárias')
                    ->numeric()
                    ->required(),
                TextInput::make('weekly_reward')
                    ->label('Recompensa Semanal')
                    ->numeric()
                    ->required(),
                FileUpload::make('image')
                    ->label('Imagem do VIP')
                    ->directory('uploads/vips')
                    ->image()
                    ->imagePreviewHeight('100')
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50),
                TextColumn::make('required_missions')
                    ->label('Missões Necessárias')
                    ->sortable(),
                TextColumn::make('weekly_reward')
                    ->label('Recompensa Semanal')
                    ->sortable(),
                TextColumn::make('image')
                    ->label('Imagem')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '';
                        $url = '/storage/' . $state;
                        return new HtmlString('<img src="' . $url . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">');
                    })
                    ->html(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVips::route('/'),
            'create' => Pages\CreateVip::route('/create'),
            'edit' => Pages\EditVip::route('/{record}/edit'),
        ];
    }
}
