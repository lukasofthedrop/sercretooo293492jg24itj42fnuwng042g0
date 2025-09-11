<?php
namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class MyBetsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'HISTÓRICO DE APOSTAS';
    protected static ?int $navigationSort = -1;
    protected int | string | array $columnSpan = 'full';
    public User $record;

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->where('user_id', $this->record->id))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('gameDetails.game_name')
                    ->label('NOME DO JOGO')
                    ->color('info')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('RESULTADO')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'Perda' => 'APOSTA PERDIDA',
                            'Ganho' => 'APOSTA GANHA',
                            default => 'DESCONHECIDO',
                        };
                    })
                    ->color(function ($state) {
                        return match($state) {
                            'Ganho' => 'success',
                            'Perda' => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('type_money')
                    ->label('CARTEIRA USADA')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'balance' => 'CARTEIRA DEPÓSITO',
                            'balance_bonus' => 'CARTEIRA BÔNUS',
                            'balance_withdrawal' => 'CARTEIRA SAQUE',
                            default => 'CARTEIRA DESCONHECIDA',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('VALOR DA APOSTA')
                    ->money('BRL')
                    ->badge()
                    ->color('success')
                    ->sortable()  // Torna a coluna ordenável
                    ->searchable(),
                Tables\Columns\TextColumn::make('providers')
                    ->label('STATUS')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'Play Fiver' => 'VALIDADO',
                            default => '',
                        };
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('APOSTADO EM')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('type_ganho')
                    ->label('APOSTAS GANHAS')
                    ->query(fn (Builder $query): Builder => $query->where('type', '=', 'win')),

                Filter::make('type_perda')
                    ->label('APOSTAS PERDIDAS')
                    ->query(fn (Builder $query): Builder => $query->where('type', '=', 'bet')),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Data Inicial'),
                        DatePicker::make('created_until')->label('Data Final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Criação Inicial ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Criação Final ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ]);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
