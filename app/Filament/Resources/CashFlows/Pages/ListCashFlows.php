<?php

namespace App\Filament\Resources\CashFlows\Pages;

use App\Filament\Resources\CashFlows\CashFlowResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCashFlows extends ListRecords
{
    protected static string $resource = CashFlowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Livewire\CashFlowStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'cash in' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'debit')),
            'cash out' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'credit')),
        ];
    }
}
