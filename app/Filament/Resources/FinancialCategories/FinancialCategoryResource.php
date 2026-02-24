<?php

namespace App\Filament\Resources\FinancialCategories;

use App\Filament\Resources\FinancialCategories\Pages\CreateFinancialCategory;
use App\Filament\Resources\FinancialCategories\Pages\EditFinancialCategory;
use App\Filament\Resources\FinancialCategories\Pages\ListFinancialCategories;
use App\Filament\Resources\FinancialCategories\Schemas\FinancialCategoryForm;
use App\Filament\Resources\FinancialCategories\Tables\FinancialCategoriesTable;
use App\Models\FinancialCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FinancialCategoryResource extends Resource
{
    protected static ?string $model = FinancialCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $recordTitleAttribute = 'FinancialCategory';
    protected static string | UnitEnum | null $navigationGroup = 'Financial Management';

    public static function form(Schema $schema): Schema
    {
        return FinancialCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancialCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinancialCategories::route('/'),
            'create' => CreateFinancialCategory::route('/create'),
            'edit' => EditFinancialCategory::route('/{record}/edit'),
        ];
    }
}
