<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('is_active',true)->pluck('name', 'id')),
                TextInput::make('sku')->required(),
                TextInput::make('price')->numeric()->required(),
                TextInput::make('cost')->numeric()->required(),
                TextInput::make('stock')->numeric()->required(),
            ]);
    }
}
