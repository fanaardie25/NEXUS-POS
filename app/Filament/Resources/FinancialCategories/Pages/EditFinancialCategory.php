<?php

namespace App\Filament\Resources\FinancialCategories\Pages;

use App\Filament\Resources\FinancialCategories\FinancialCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinancialCategory extends EditRecord
{
    protected static string $resource = FinancialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->hidden(fn ($record) => $record->slug === 'modal-awal'),
        ];
    }
}
