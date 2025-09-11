<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyBonusConfigResource\Pages;
use App\Models\DailyBonusConfig;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailyBonusConfigResource extends Resource
{
    protected static ?string $model = DailyBonusConfig::class;

    protected static ?string $navigationIcon   = 'heroicon-o-ticket';
    protected static ?string $label            = 'Bônus Diário';
    protected static ?string $pluralLabel      = 'Bônus Diário'; // ou "Bônus Diários"
    protected static ?string $navigationLabel  = 'Config. Bônus Diário';
    protected static ?string $navigationGroup  = 'Finanças';

    /**
     * Restringe acesso a admins, por exemplo.
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Formulário (campos editáveis) de edição/criação do registro.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('bonus_value')
                ->label('Valor do Bônus')
                ->numeric()
                ->step(0.01)
                ->default(0)
                ->required(),

            Forms\Components\TextInput::make('cycle_hours')
                ->label('Intervalo (Horas)')
                ->numeric()
                ->default(24)
                ->minValue(1)
                ->required()
                ->helperText('Ex: 12 ou 24 horas.'),
        ]);
    }

    /**
     * Tabela de colunas, se fosse exibir, mas neste caso vamos usar apenas "Edit" direto.
     *
     * Vamos manter algo mínimo, e redirecionar para a edição.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bonus_value')
                    ->label('Bônus (R$)'),
                Tables\Columns\TextColumn::make('cycle_hours')
                    ->label('Intervalo (h)'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultPagination(1);
    }

    /**
     * Força a query para pegar somente 1 registro.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->limit(1);
    }

    /**
     * Define as rotas/páginas do Filament.
     * Em vez de create/index/edit separados, faremos como no Mines: "index" => EditDailyBonusConfig
     */
    public static function getPages(): array
    {
        return [
            // Ao acessar /daily-bonus-configs, irá diretamente para a página de editar
            'index' => Pages\EditDailyBonusConfig::route('/'),
        ];
    }
}
