<?php

namespace App\Filament\Resources\CashFlows;

use App\Filament\Resources\CashFlows\Pages\CreateCashFlow;
use App\Filament\Resources\CashFlows\Pages\EditCashFlow;
use App\Filament\Resources\CashFlows\Pages\ListCashFlows;
use App\Filament\Resources\CashFlows\Schemas\CashFlowForm;
use App\Filament\Resources\CashFlows\Tables\CashFlowsTable;
use App\Models\CashFlow;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CashFlowResource extends Resource
{
    protected static ?string $model = CashFlow::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?string $recordTitleAttribute = 'CashFlow';
    protected static string | UnitEnum | null $navigationGroup = 'Financial Management';



    public static function form(Schema $schema): Schema
    {
        return CashFlowForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashFlowsTable::configure($table);
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
            'index' => ListCashFlows::route('/'),
            'create' => CreateCashFlow::route('/create'),
            'edit' => EditCashFlow::route('/{record}/edit'),
        ];
    }
}
