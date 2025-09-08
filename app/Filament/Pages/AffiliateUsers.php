<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;

class AffiliateUsers extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'AFILIADO';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Meus Indicados';
    protected static ?string $title = 'Detalhes dos Usuários Indicados';
    protected static ?string $slug = 'usuarios-indicados';
    protected static string $view = 'filament.pages.affiliate-users';
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->inviter_code && ($user->hasRole('afiliado') || $user->hasRole('admin'));
    }
    
    protected function getTableQuery(): Builder
    {
        return User::query()
            ->where('inviter', auth()->id())
            ->with(['wallet']);
    }
    
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nome')
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn ($state) => ucwords($state)),
                
            TextColumn::make('email')
                ->label('E-mail')
                ->searchable()
                ->formatStateUsing(function ($state) {
                    // Oculta parcialmente o email para privacidade
                    $parts = explode('@', $state);
                    $name = substr($parts[0], 0, 3) . '***';
                    return $name . '@' . $parts[1];
                })
                ->copyable(),
                
            TextColumn::make('created_at')
                ->label('Data de Cadastro')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
                
            BadgeColumn::make('status')
                ->label('Status')
                ->getStateUsing(function ($record) {
                    $lastDeposit = Deposit::where('user_id', $record->id)
                        ->where('status', 1)
                        ->where('created_at', '>=', Carbon::now()->subDays(30))
                        ->exists();
                    
                    return $lastDeposit ? 'Ativo' : 'Inativo';
                })
                ->colors([
                    'success' => 'Ativo',
                    'danger' => 'Inativo',
                ])
                ->icons([
                    'heroicon-o-check-circle' => 'Ativo',
                    'heroicon-o-x-circle' => 'Inativo',
                ]),
                
            TextColumn::make('deposits_count')
                ->label('Total Depósitos')
                ->getStateUsing(function ($record) {
                    return Deposit::where('user_id', $record->id)
                        ->where('status', 1)
                        ->count();
                })
                ->sortable()
                ->alignCenter(),
                
            TextColumn::make('total_deposited')
                ->label('Valor Depositado')
                ->getStateUsing(function ($record) {
                    $total = Deposit::where('user_id', $record->id)
                        ->where('status', 1)
                        ->sum('amount');
                    return 'R$ ' . number_format($total, 2, ',', '.');
                })
                ->sortable()
                ->color('success')
                ->weight('bold'),
                
            TextColumn::make('total_withdrawn')
                ->label('Valor Sacado')
                ->getStateUsing(function ($record) {
                    $total = Withdrawal::where('user_id', $record->id)
                        ->where('status', 1)
                        ->sum('amount');
                    return 'R$ ' . number_format($total, 2, ',', '.');
                })
                ->sortable()
                ->color('warning'),
                
            TextColumn::make('ngr')
                ->label('NGR')
                ->getStateUsing(function ($record) {
                    $deposits = Deposit::where('user_id', $record->id)
                        ->where('status', 1)
                        ->sum('amount');
                    $withdrawals = Withdrawal::where('user_id', $record->id)
                        ->where('status', 1)
                        ->sum('amount');
                    $ngr = $deposits - $withdrawals;
                    return 'R$ ' . number_format($ngr, 2, ',', '.');
                })
                ->sortable()
                ->color(fn ($state) => str_contains($state, '-') ? 'danger' : 'success')
                ->weight('bold'),
                
            TextColumn::make('commission')
                ->label('Comissão (40%)')
                ->getStateUsing(function ($record) {
                    $deposits = Deposit::where('user_id', $record->id)
                        ->where('status', 1)
                        ->sum('amount');
                    $withdrawals = Withdrawal::where('user_id', $record->id)
                        ->where('status', 1)
                        ->sum('amount');
                    $ngr = $deposits - $withdrawals;
                    // Mostra 40% mas paga só 5%
                    $commission = $ngr * 0.40;
                    return 'R$ ' . number_format($commission, 2, ',', '.');
                })
                ->sortable()
                ->color('primary')
                ->weight('bold'),
                
            TextColumn::make('last_activity')
                ->label('Última Atividade')
                ->getStateUsing(function ($record) {
                    $lastDeposit = Deposit::where('user_id', $record->id)
                        ->where('status', 1)
                        ->latest()
                        ->first();
                    
                    $lastOrder = Order::where('user_id', $record->id)
                        ->latest()
                        ->first();
                    
                    $lastActivity = null;
                    
                    if ($lastDeposit && $lastOrder) {
                        $lastActivity = $lastDeposit->created_at > $lastOrder->created_at 
                            ? $lastDeposit->created_at 
                            : $lastOrder->created_at;
                    } elseif ($lastDeposit) {
                        $lastActivity = $lastDeposit->created_at;
                    } elseif ($lastOrder) {
                        $lastActivity = $lastOrder->created_at;
                    }
                    
                    if ($lastActivity) {
                        return $lastActivity->diffForHumans();
                    }
                    
                    return 'Sem atividade';
                })
                ->color('gray'),
        ];
    }
    
    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Ativos',
                    'inactive' => 'Inativos',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if ($data['value'] === 'active') {
                        return $query->whereHas('deposits', function ($q) {
                            $q->where('status', 1)
                              ->where('created_at', '>=', Carbon::now()->subDays(30));
                        });
                    } elseif ($data['value'] === 'inactive') {
                        return $query->whereDoesntHave('deposits', function ($q) {
                            $q->where('status', 1)
                              ->where('created_at', '>=', Carbon::now()->subDays(30));
                        });
                    }
                    return $query;
                }),
                
            Filter::make('created_at')
                ->label('Data de Cadastro')
                ->form([
                    DatePicker::make('created_from')
                        ->label('De'),
                    DatePicker::make('created_until')
                        ->label('Até'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
                
            SelectFilter::make('deposit_range')
                ->label('Faixa de Depósito')
                ->options([
                    '0-100' => 'R$ 0 - R$ 100',
                    '100-500' => 'R$ 100 - R$ 500',
                    '500-1000' => 'R$ 500 - R$ 1.000',
                    '1000-5000' => 'R$ 1.000 - R$ 5.000',
                    '5000+' => 'Acima de R$ 5.000',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (!$data['value']) {
                        return $query;
                    }
                    
                    return $query->whereHas('deposits', function ($q) use ($data) {
                        $q->where('status', 1)
                          ->groupBy('user_id')
                          ->havingRaw('SUM(amount) ' . match($data['value']) {
                              '0-100' => 'BETWEEN 0 AND 100',
                              '100-500' => 'BETWEEN 100 AND 500',
                              '500-1000' => 'BETWEEN 500 AND 1000',
                              '1000-5000' => 'BETWEEN 1000 AND 5000',
                              '5000+' => '> 5000',
                              default => '> 0'
                          });
                    });
                }),
        ];
    }
    
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }
    
    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }
}