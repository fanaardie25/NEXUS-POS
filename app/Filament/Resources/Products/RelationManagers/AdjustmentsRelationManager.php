<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdjustmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'adjustments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
               Select::make('type')
                    ->options([
                        'addition' => 'in',
                        'subtraction' => 'out',
                    ])
                    ->required()
                    ->native(false), 
                
                TextInput::make('qty')
                    ->label('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(fn ($get, $record) => $get('type') === 'subtraction' ? $this->getOwnerRecord()->stock : null)
                     ->hint(fn ($get) => $get('type') === 'subtraction' ? 'Stok available: ' . $this->getOwnerRecord()->stock : null),

                Textarea::make('description')
                    ->label('reason')
                    ->required()
                    ->placeholder('Contoh: Restock supplier atau Barang rusak')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('Adjustment Type'),
                TextColumn::make('qty')->label('Quantity'),
                TextColumn::make('description')->label('Description'),
                TextColumn::make('created_at')->label('Date')->date(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
