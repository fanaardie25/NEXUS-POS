<?php

namespace App\Filament\Widgets;

use App\Models\CashFlow;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashFlowStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
            
        $openingBalance = CashFlow::whereHas('financialCategory', fn($q) => 
            $q->where('slug', 'modal-awal')
        )->sum('amount');

        $pureIncome = CashFlow::where('type', 'debit')
            ->whereDoesntHave('financialCategory', fn($q) => 
                $q->where('slug', 'modal-awal')
            )->sum('amount');

        $expense = CashFlow::where('type', 'credit')->sum('amount');
        $netBalance = ($openingBalance + $pureIncome) - $expense;

        return [
            Stat::make('Total Income', 'IDR ' . number_format($pureIncome, 0, ',', '.'))
                ->description('Total money received')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Expense', 'IDR ' . number_format($expense, 0, ',', '.'))
                ->description('Total money spent')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Net Balance', 'IDR ' . number_format($netBalance, 0, ',', '.'))
                ->description('Current cash on hand')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
