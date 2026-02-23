<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Marcelorodrigo\FilamentBarcodeScannerField\Forms\Components\BarcodeInput;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')->label('Product Image')->disk('public')->directory('product-images'),
                TextInput::make('name')->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->required()
                    ->options(Category::where('is_active',true)->pluck('name', 'id')),
                BarcodeInput::make('barcode')
                ->icon('heroicon-o-arrow-right')
                ->label('Product Barcode')
                ->placeholder('Scan or type barcode...')
                ->required()
                ->unique('products', 'barcode')
                ->rules(['min:8', 'max:50'])
                ->helperText('Scan the barcode on the product packaging')
                ->hint('Required')
                ->live(),
                TextInput::make('sku')->required(),
                TextInput::make('price')->numeric()->required(),
                TextInput::make('cost')->numeric()->required(),
                TextInput::make('stock')->numeric()->required(),
            ]);
    }
}
