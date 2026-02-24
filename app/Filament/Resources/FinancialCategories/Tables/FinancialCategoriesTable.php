<?php

namespace App\Filament\Resources\FinancialCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class FinancialCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('slug')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state === 'debit' ? 'Cash in (Debit)' : 'Cash out (Credit)')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => $state === 'debit',
                        'danger' => fn ($state) => $state === 'credit',
                    ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn ($record) => $record->slug === 'modal-awal'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
