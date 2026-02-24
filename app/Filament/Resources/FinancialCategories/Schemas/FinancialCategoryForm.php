<?php

namespace App\Filament\Resources\FinancialCategories\Schemas;

use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FinancialCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->live(onBlur: false),
                \Filament\Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique('financial_categories', 'slug', ignoreRecord: true)
                    ->readOnly(),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'debit' => 'Cash In (Debit)',
                        'credit' => 'Cash Out (Credit)',
                    ])
                    ->required()
                    ->native(false),
            ])->columns(2);
    }
}
