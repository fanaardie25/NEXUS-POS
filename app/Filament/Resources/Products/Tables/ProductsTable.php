<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Exports\ProductExporter;
use App\Filament\Imports\ProductImporter;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ImportAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->searchable()->sortable(),
                TextColumn::make('sku')->searchable()->sortable(),
                TextColumn::make('stock')->numeric()->sortable()
                ->badge()
                 ->color(fn (bool $state): string => match ($state) {
                    false => 'danger',
                    true => 'success',
                })
                ->numeric(decimalPlaces: 0),
                TextColumn::make('price')->numeric()->sortable()
                ->badge()
                ->money('IDR'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                     
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                ImportAction::make('import products')
                    ->importer(ProductImporter::class)
                    ->label('Import Products')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('primary'),
                Action::make('template')
                ->label('Download Template')
                ->color('warning')
                ->url(route('products.template'))
                ->openUrlInNewTab()
             ])
            ->recordActions([
                EditAction::make(),
                 ForceDeleteAction::make(),
                  RestoreAction::make(),
                DeleteAction::make(),
                Action::make('printBarcode')
                ->label('Print Barcode')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->hidden(fn ($record) => !$record->barcode)
                ->url(fn ($record) => route('print.barcode', $record))
                ->openUrlInNewTab(), 
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('printSelectedBarcodes')
                    ->label('Print Seleected Barcodes')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (Collection $records) {
                        $ids = $records->pluck('id')->implode(',');
                        
                        return redirect()->route('print.barcodes.bulk', ['ids' => $ids]);
                    })->openUrlInNewTab(), 
                    BulkAction::make('toggle')
                        ->label('Toggle Active')
                        ->accessSelectedRecords()
                        ->icon('heroicon-o-arrows-right-left')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'is_active' => ! $record->is_active,
                                ]);
                            });
                        })
                ]),
            ]);
    }
}
