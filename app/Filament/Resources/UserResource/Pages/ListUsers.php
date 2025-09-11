<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;


class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\UserOverview::class
        ];
    }

    public function getTabs(): array
    {
        $afiliados = \DB::table('model_has_roles')
            ->where('role_id', 2)
            ->pluck('model_id')
            ->toArray();

        $admins = \DB::table('model_has_roles')
            ->where('role_id', 1)
            ->pluck('model_id')
            ->toArray();

        return [
            'all' => Tab::make('USUÁRIOS CADASTRADOS')
                ->modifyQueryUsing(function ($query) {
                    return $query;
                }),

            'admins' => Tab::make('ADMINISTRADORES ')
                ->modifyQueryUsing(function ($query) use ($admins) {
                    return $query->whereIn('id', $admins);
                }),
            'affiliates' => Tab::make('AFILIADOS CADASTRADOS')
                ->modifyQueryUsing(function ($query) use ($afiliados) {
                    return $query->whereIn('id', $afiliados);
                }),
        ];
    }
}
