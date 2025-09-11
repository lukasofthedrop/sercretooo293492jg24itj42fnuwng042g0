<?php

namespace App\Filament\Pages\AffiliateHistory\Pages\Actions;

use Filament\Tables\Actions\Action;

use App\Filament\Pages\AffiliateHistory;
use App\Filament\Pages\DetailsAffiliate;
use App\Filament\Resources\UserResource;
use App\Models\AffiliateLogs;
use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\TextInput;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\View\Components\Modal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


class DetailsAction extends Action 
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'Detalhes';
    }
  
    protected function setUp(): void
    {
        parent::setUp();
       

        $this->label(__('Detalhes'));
       
        $this->icon('heroicon-o-chart-bar');
        $this->url(function($record){
            session()->put('providerId', $record->id);

            return route(DetailsAffiliate::getRouteName(), ["provider" => $record->id]);
        });
        $this->openUrlInNewTab();
       
    }
   
   
    
    
   
}
