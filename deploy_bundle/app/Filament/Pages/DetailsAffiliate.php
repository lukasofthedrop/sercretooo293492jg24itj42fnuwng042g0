<?php

namespace App\Filament\Pages;

use App\Models\AffiliateHistory as ModelsAffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\GamesKey;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Contracts\HasTable;

use Filament\Pages\Page;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class DetailsAffiliate extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.affiliateDetails';

    protected static ?string $title = 'Histórico do afiliado';
    protected static ?string $model = AffiliateLogs::class;

    protected static ?string $slug = 'afiliado/details/{provider}';
    protected ?string $id = null;
  
    public ?array $data = [
        "id" => null
    ];

    /**
     * @dev  
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla o acesso total à página
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla a visualização de elementos específicos
    }
 
    public function mount($provider){
        if($this->data['id'] == null){
            $this->data['id'] = $provider;
        }
    }
 

    public function table(Table $table): Table
    {  
       
        return $table
        ->query(self::$model::query())
       
        ->columns([

            TextColumn::make("commission_type")->label("Tipo de Comissão")->formatStateUsing(function($record) {
                if ($record->commission_type == "revshare") {
                    return "RevShare";
                } else {
                    return "CPA";
                }  
            })->default("Indefinido"),
            TextColumn::make("commission")->label("Valor da comissão")->formatStateUsing(function($record) {
                $count = number_format($record->commission, 2, ",", ",");
                
                return "R$". $count;
            })->default(0),
            TextColumn::make("type")->label("Tipo")->formatStateUsing(function($record){
                if($record->type == "decrement"){
                    return "Ganho";
                }else{
                    return "Perca";
                }
            }),
            TextColumn::make("created_at")->label("Data")->dateTime()
           

        ])->actions([
          
            
        ])->filters([
            Filter::make('created_at')
            
            ->form([
                DatePicker::make('created_from')->label("Criado a partir de"),
                DatePicker::make('created_until')->label("Criado até"),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                  
                    ->where("user_id",$this->data['id'])
                    ->when(
                        $data['created_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                    )
                    ->when(
                        $data['created_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                    );
                   

                    })
            ->indicateUsing(function (array $data): array {
                $indicators = [];

                if ($data['created_from'] ?? null) {
                    $indicators['created_from'] = 'Criado a partir de ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                }

                if ($data['created_until'] ?? null) {
                    $indicators['created_until'] = 'Criado até ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                }

                return $indicators;
            })
        ]);



    
    }

 
}
