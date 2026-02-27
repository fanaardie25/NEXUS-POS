<?php

namespace App\Filament\Widgets;

use App\Models\CashFlow;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ? Carbon::parse($this->filters['startDate']) : now()->startOfMonth();
        $endDate = $this->filters['endDate'] ? Carbon::parse($this->filters['endDate']) : now()->endOfMonth();

        // 1. SALDO SAAT INI (Total semua Debit - Total semua Credit)
        // Ini tidak terpengaruh filter tanggal (menunjukkan kondisi real-time saat ini)
        $totalDebit = CashFlow::where('type', 'debit')->sum('amount');
        $totalCredit = CashFlow::where('type', 'credit')->sum('amount');
        $currentBalance = $totalDebit - $totalCredit;

        // 2. TOTAL PENGELUARAN (Berdasarkan Filter Tanggal)
        $pengeluaran = CashFlow::query()
            ->where('type', 'credit') 
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 3. OMZET (Debit minus Modal Awal - Berdasarkan Filter Tanggal)
        $omzet = CashFlow::query()
            ->where('type', 'debit')
            ->whereDoesntHave('financialCategory', function ($query) {
                $query->where('slug', 'modal-awal');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 4. LABA BERSIH (Margin Produk - Pengeluaran - Berdasarkan Filter Tanggal)
        $grossProfit = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transaction_items.created_at', [$startDate, $endDate])
            ->selectRaw('SUM((transaction_items.price - products.cost) * transaction_items.quantity) as profit')
            ->value('profit') ?? 0;
        
        $netProfit = $grossProfit - $pengeluaran;

        return [
            Stat::make('Saldo Saat Ini', 'IDR ' . number_format($currentBalance, 0, ',', '.'))
                ->description('Total uang di laci/bank')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success'),

            Stat::make('Total Pengeluaran', 'IDR ' . number_format($pengeluaran, 0, ',', '.'))
                ->description('Beban operasional periode ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Total Omzet', 'IDR ' . number_format($omzet, 0, ',', '.'))
                ->description('Penjualan (Tanpa Modal Awal)')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Laba Bersih', 'IDR ' . number_format($netProfit, 0, ',', '.'))
                ->description($netProfit >= 0 ? 'Keuntungan bersih' : 'Kerugian operasional')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-check-badge' : 'heroicon-m-x-circle')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}