<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransactionsTable
{
    
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()->sortable(),
               TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable() 
                    ->copyMessage('Invoice number copied') 
                    ->copyMessageDuration(1500),
                TextColumn::make('user.name')->label('cashier')->searchable()->sortable(),
                TextColumn::make('total')->sortable()->money('idr'),
                TextColumn::make('payment_method')->badge(),
                TextColumn::make('created_at')->label('date')->sortable()->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exports([
                ExcelExport::make()
                    ->withFilename('Transactions-' . date('Y-m-d')) 
                    ->withColumns([
                        Column::make('invoice_number')
                            ->heading('Invoice Number'),
                            
                        Column::make('user.name')
                            ->heading('Cashier'),
                            
                        Column::make('total')
                            ->heading('Total Amount')
                            ->format('#,##0'), 
                            
                        Column::make('payment_method')
                            ->heading('Payment'),
                            
                        Column::make('created_at')
                            ->heading('Transaction Date')
                            ->format('d/m/Y H:i'), 
                    ])
])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
