<?php

namespace App\Livewire;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransactionStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
       return [
           Stat::make('Total Sales Today', 'IDR ' . number_format(Transaction::whereDate('created_at', Carbon::today())->sum('total'), 0, ',', '.'))
                ->description('Total uang masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-m-currency-dollar'),

            Stat::make('New Orders', Transaction::whereDate('created_at', Carbon::today())->count())
                ->description('Jumlah transaksi masuk hari ini')
                ->icon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Active Cashiers', Transaction::whereDate('created_at', Carbon::today())->distinct('cashier_id')->count('cashier_id'))
                ->description('Jumlah kasir yang melakukan transaksi hari ini')
                ->icon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
