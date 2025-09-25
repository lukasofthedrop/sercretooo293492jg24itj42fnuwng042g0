<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SettingResource extends Resource
{

    protected Setting $record;

    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    /**
     * @dev  
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * @dev  
     * @return void
     */
    public function mount(): void
    {
        $this->record = Setting::first();
    }

    /**
     * @param Model $record
     * @return FilamentPageSidebar
     */
    public static function sidebar(Model $record): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
        
            ->setTitle('CONFIGURAÇÕES DA PLATAFORMA')
            ->setDescription('Ajuste as configurações da plataforma')
            ->setNavigationItems([
                PageNavigationItem::make('LOGOTIPO E NOME')
                    ->translateLabel()
                    ->url(static::getUrl('index'))->icon('heroicon-o-computer-desktop')
                    ->isActiveWhen(function () {
                        return request()->routeIs(static::getRouteBaseName() . '.index');
                    }),

                 PageNavigationItem::make('ROLL-OVER')
                     ->translateLabel()
                     ->url(static::getUrl('rollover', ['record' => $record->id]))->icon('heroicon-o-shield-check')
                     ->isActiveWhen(function () {
                         return request()->routeIs(static::getRouteBaseName() . '.rollover');
                     }),
                PageNavigationItem::make('LIMITE DE SAQUE')
                    ->translateLabel()
                    ->url(static::getUrl('limit', ['record' => $record->id]))->icon('heroicon-o-hand-raised')
                    ->isActiveWhen(function () {
                        return request()->routeIs(static::getRouteBaseName() . '.limit');
                    }),
                PageNavigationItem::make('CENTRAL FINANCEIRA')
                    ->translateLabel()
                    ->url(static::getUrl('payment', ['record' => $record->id]))->icon('heroicon-o-currency-dollar')
                    ->isActiveWhen(function () {
                        return request()->routeIs(static::getRouteBaseName() . '.payment');
                    }),
            ]);
    }

    /**
     *
     * @dev  
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * @dev  
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            ]);
    }

    /**
     * @return array|\Filament\Resources\RelationManagers\RelationGroup[]|\Filament\Resources\RelationManagers\RelationManagerConfiguration[]|string[]
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * @dev  
     * @return array|\Filament\Resources\Pages\PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\DefaultSetting::route('/'),
            'limit' => Pages\LimitSetting::route('/limit/{record}'),
            'rollover' => Pages\RolloverSetting::route('/rollover/{record}'),
            'details' => Pages\DefaultSetting::route('/details/{record}'),
            'payment' => Pages\PaymentSetting::route('/payment/{record}'),
        ];
    }
}
