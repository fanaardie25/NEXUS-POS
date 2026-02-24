<?php

namespace App\Filament\Resources\CashFlows\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class CashFlowsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('description')->searchable(),
                TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state === 'debit' ? 'Cash in (Debit)' : 'Cash out (Credit)')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => $state === 'debit',
                        'danger' => fn ($state) => $state === 'credit',
                    ]),
                TextColumn::make('amount')->money('idr', true),
                TextColumn::make('financialCategory.name')->label('Category')->searchable(),
                TextColumn::make('trackable_type')
                    ->label('Data Source')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'Manual Input';

                        return match ($state) {
                            'App\Models\Transaction' => 'Customer Order',
                            default                  => class_basename($state),
                        };
                    }),
                TextColumn::make('trackable_id')->label('Id Reference'),
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
            ->headerActions([
                 ExportAction::make()->exports([
                    ExcelExport::make('table')->fromTable()->askForWriterType()->askForFilename()->queue(),
                ])
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
