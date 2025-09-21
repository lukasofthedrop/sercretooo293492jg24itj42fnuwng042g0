<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistributionSystemResource\Pages;
use App\Models\DistributionSystem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class DistributionSystemResource extends Resource
{
    /**
     * Modelo Eloquent que este recurso gerencia
     */
    protected static ?string $model = DistributionSystem::class;

    /**
     * Customizações de exibição no menu do Filament
     */
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $label = 'Distribuição de Ganhos';
    protected static ?string $pluralLabel = 'Sistema de Distribuição';
    protected static ?string $navigationLabel = 'Configuração de Distribuição';
    protected static ?string $navigationGroup = 'Finanças';

    /**
     * Controla o acesso: somente Admin pode ver
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Definição do formulário para edição do registro
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('ativo')
                    ->label('Sistema Ativado')
                    ->helperText('Ligue ou desligue o sistema de distribuição.')
                    ->live(),

                Forms\Components\TextInput::make('meta_arrecadacao')
                    ->label('Meta de Arrecadação')
                    ->numeric()
                    ->step(1)
                    ->rules(['integer']),

                Forms\Components\TextInput::make('percentual_distribuicao')
                    ->label('% de Distribuição')
                    ->numeric()
                    ->step(1)
                    ->rules(['integer']),


                Forms\Components\TextInput::make('rtp_arrecadacao')
                    ->label('RTP de Arrecadação')
                    ->numeric()
                    ->step(1)
                    ->rules(['integer']),
                Forms\Components\TextInput::make('rtp_distribuicao')
                    ->label('RTP de Distribuição')
                    ->numeric()
                    ->step(1)
                    ->rules(['integer']),

                Forms\Components\TextInput::make('total_arrecadado')
                    ->label('Total Arrecadado')
                    ->numeric()
                    ->disabled(),

                Forms\Components\TextInput::make('total_distribuido')
                    ->label('Total Distribuído')
                    ->numeric()
                    ->disabled(),

                Forms\Components\Select::make('modo')
                    ->label('Modo Atual')
                    ->options([
                        'arrecadacao' => 'Arrecadação',
                        'distribuicao' => 'Distribuição',
                    ])
                    ->disabled(),
            ]);
    }

    /**
     * Opcional: Tabela de visualização (caso queira ver o registro),
     * mas sem permitir criação ou exclusão.
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('ativo')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Ativado' : 'Desativado')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('meta_arrecadacao')
                    ->label('Meta de Arrecadação'),

                Tables\Columns\TextColumn::make('percentual_distribuicao')
                    ->label('% de Distribuição'),

                Tables\Columns\TextColumn::make('rtp_arrecadacao')
                    ->label('RTP de Arrecadação'),

                Tables\Columns\TextColumn::make('rtp_distribuicao')
                    ->label('RTP de Distribuição'),

                Tables\Columns\TextColumn::make('total_arrecadado')
                    ->label('Total Arrecadado'),

                Tables\Columns\TextColumn::make('total_distribuido')
                    ->label('Total Distribuído'),

                Tables\Columns\BadgeColumn::make('modo')
                    ->label('Modo Atual')
                    ->formatStateUsing(fn ($state) => $state === 'arrecadacao' ? 'Arrecadação' : 'Distribuição')
                    ->color(fn ($state) => $state === 'arrecadacao' ? 'primary' : 'success'),
            ])
            ->actions([
                // Só permitimos edição do único registro
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]) // sem ações em lote
            ->defaultPagination(1); // exibe no máximo 1 registro
    }

    /**
     * Importante: forçar a query a retornar somente um registro.
     */
    public static function getEloquentQuery(): Builder
    {
        // limit(1) para não listar múltiplos
        return parent::getEloquentQuery()->limit(1);
    }

    /**
     * Redefine as páginas disponíveis:
     *  - 'index' => redirecionado para a tela de edição
     */
    public static function getPages(): array
    {
        return [
            // Ao acessar /distribution-systems, irá diretamente para EditDistributionSystem
            'index' => Pages\EditDistributionSystem::route('/'),
        ];
    }
}
