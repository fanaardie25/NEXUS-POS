<?php

namespace App\Filament\Resources\CashFlows\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CashFlowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Transaksi Kas')
                ->schema([
                    DatePicker::make('date')
                        ->default(now())
                        ->required(),
                        
                    Select::make('type')
                        ->options([
                            'debit' => 'Cash In (Debit)',
                            'credit' => 'Cash Out (Credit)',
                        ])
                        ->required()
                        ->native(false)
                        ->live(onBlur: false)
                        ->afterStateUpdated(fn (Set $set) => $set('financial_category_id', null)), 

                    Select::make('financial_category_id')
                        ->relationship(
                                name: 'financialCategory', 
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Get $get) {
                                    $type = $get('type');
                                    if ($type) {
                                        $query->where('type', $type);
                                    }
                                }
                            )
                        ->label('category')
                        ->searchable()
                        ->preload()
                        ->disabled(fn (Get $get) => !$get('type')) 
                        ->required()
                        ,

                    TextInput::make('amount')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    Textarea::make('description')
                        ->columnSpanFull(),
                ])->columns(2),
                
            Section::make('Information System')
                ->description('this data is automatically generated from the system, if you want to edit it, please edit the source data')
                ->schema([
                    Placeholder::make('trackable_type')
                        ->label('Data Source')
                        ->content(function ($record) {
                            if (!$record?->trackable_type) return 'Manual Input';

                            return match ($record->trackable_type) {
                                'App\Models\Transaction' => 'Customer Order',
                                default                  => class_basename($record->trackable_type),
                            };
                        }),
                        
                    Placeholder::make('trackable_id')
                        ->label('Referensi ID')
                        ->content(fn ($record) => $record?->trackable_id ?? '-'),
                ])
                ->hidden(fn ($record) => $record === null)
                ->columns(2),
            ]);
    }
}
