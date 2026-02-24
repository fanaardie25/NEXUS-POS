<?php

namespace App\Filament\Resources\FinancialCategories\Pages;

use App\Filament\Resources\FinancialCategories\FinancialCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialCategory extends CreateRecord
{
    protected static string $resource = FinancialCategoryResource::class;
}
