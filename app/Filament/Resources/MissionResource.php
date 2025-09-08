<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MissionResource\Pages;
use App\Models\Mission;
use Filament\Forms;
use Filament\Forms\Components\{FileUpload, Select, TextInput, Textarea};
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Filament\Tables\Table;

class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $label = 'Missão';
    protected static ?string $navigationLabel = 'Gerenciar Missão';
    protected static ?string $pluralLabel = 'Missões';
    protected static ?string $navigationGroup = 'Marketing';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Descrição')
                ->rows(5)
                ->nullable(),

            Select::make('type')
                ->label('Tipo de Missão')
                ->options([
                    'deposit' => 'Missão de Depósito',
                    'game_bet' => 'Missão de Aposta por Jogo',
                    'total_bet' => 'Missão de Aposta Total',
                    'rounds_played' => 'Missão de Rodadas Jogadas',
                    'win_amount' => 'Missão de Ganho por Jogo',
                    'loss_amount' => 'Missão de Perda por Jogo',
                ])
                ->required()
                ->reactive() // Atualiza dinamicamente ao selecionar
                ->afterStateUpdated(function (callable $set, $state) {
                    // Reseta o campo game_id se o tipo não for 'game_bet'
                    if ($state !== 'game_bet') {
                        $set('game_id', null);
                    }
                }),

            Select::make('game_id')
                ->label('Selecione o Jogo')
                ->options(
                    \DB::table('games')->pluck('game_name', 'game_id')->toArray() // Pluck com game_id como chave e game_name como valor
                )
                ->searchable()
                ->nullable()
                ->visible(fn ($get) => in_array($get('type'), ['game_bet', 'rounds_played', 'win_amount', 'loss_amount'])), // Mostra para tipos que envolvem jogos

            TextInput::make('target_amount')
                ->label('Valor-Alvo (R$ ou Rodadas)')
                ->numeric()
                ->helperText(fn ($get) => match ($get('type')) {
                    'rounds_played' => 'Número de rodadas que o usuário precisa jogar.',
                    'win_amount' => 'Valor de ganho que o usuário precisa atingir.',
                    'loss_amount' => 'Valor de perda que o usuário precisa atingir.',
                    default => 'Valor que o usuário precisa alcançar para concluir a missão.',
                })
                ->required(),

            TextInput::make('reward')
                ->label('Recompensa (R$)')
                ->numeric()
                ->helperText('Valor que o usuário receberá ao concluir a missão.')
                ->required(),

            FileUpload::make('image')
                ->label('Imagem da Missão')
                ->image()
                ->directory('/uploads/missoes') // Diretório onde as imagens serão salvas
                ->placeholder('Carregue uma imagem'),

            Select::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Ativa',
                    'inactive' => 'Inativa',
                ])
                ->default('active')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('image')
                ->label('Imagem')
                ->formatStateUsing(function ($state) {
                    if (!$state) return '';
                    // Imagens de missões já incluem o caminho completo
                    $url = str_starts_with($state, 'uploads/') ? '/storage/' . $state : '/storage/' . $state;
                    return new HtmlString('<img src="' . $url . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">');
                })
                ->html(),
            TextColumn::make('title')
                ->label('Título')
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label('Tipo de Missão')
                ->formatStateUsing(fn ($state) => [
                    'deposit' => 'Missão de Depósito',
                    'game_bet' => 'Missão de Aposta por Jogo',
                    'total_bet' => 'Missão de Aposta Total',
                    'rounds_played' => 'Missão de Rodadas Jogadas',
                    'win_amount' => 'Missão de Ganho por Jogo',
                    'loss_amount' => 'Missão de Perda por Jogo',
                ][$state] ?? $state),
            TextColumn::make('target_amount')
                ->label('Valor-Alvo')
                ->formatStateUsing(fn ($state, $record) => match ($record->type) {
                    'rounds_played' => "{$state} Rodadas",
                    default => "R$ {$state}",
                }),
            TextColumn::make('reward')
                ->label('Recompensa')
                ->money('BRL'),
            TextColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => [
                    'active' => 'Ativa',
                    'inactive' => 'Inativa',
                ][$state] ?? $state),
        ])->filters([])
          ->actions([
              Tables\Actions\EditAction::make(),
          ])->bulkActions([
              Tables\Actions\DeleteBulkAction::make(),
          ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMissions::route('/'),
            'create' => Pages\CreateMission::route('/create'),
            'edit' => Pages\EditMission::route('/{record}/edit'),
        ];
    }
}
