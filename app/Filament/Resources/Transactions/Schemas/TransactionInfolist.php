<?php

namespace App\Filament\Resources\Transactions\Schemas;


use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Summary')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('invoice_number')->label('Order ID'),
                        TextEntry::make('created_at')->label('Transaction Date')->dateTime(),
                        TextEntry::make('user.name')->label('Cashier'),
                        TextEntry::make('payment_method')->badge(),
                        TextEntry::make('total')->money('idr'),
                    ]),

                Section::make('Ordered Items')
                    ->schema([
                        RepeatableEntry::make('items') 
                            ->schema([
                                Grid::make(4) 
                                    ->schema([
                                        TextEntry::make('product.name')->label('Product Name'),
                                        TextEntry::make('price')->money('idr'),
                                        TextEntry::make('quantity')->suffix(' pcs'),
                                        TextEntry::make('subtotal')->money('idr'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
