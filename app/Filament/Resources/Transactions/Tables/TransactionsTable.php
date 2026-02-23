<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
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
                Filter::make('created_at')
                ->schema([
                    DatePicker::make('from')->label('Date From'),
                    DatePicker::make('until')->label('Date Until'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q) =>
                            $q->whereDate('created_at', '>=', $data['from'])
                        )
                        ->when($data['until'], fn ($q) =>
                            $q->whereDate('created_at', '<=', $data['until'])
                        );
                }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->headerActions([
              ExportAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->askForWriterType()->askForFilename()->queue(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
