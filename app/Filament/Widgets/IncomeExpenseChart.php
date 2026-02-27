<?php

namespace App\Filament\Widgets;

use App\Models\CashFlow;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class IncomeExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Tren Keuangan';
    protected int | string | array $columnSpan = 2;
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ? Carbon::parse($this->filters['startDate']) : now()->startOfMonth();
        $endDate = $this->filters['endDate'] ? Carbon::parse($this->filters['endDate']) : now()->endOfMonth();

        // Fungsi pembantu untuk ambil data harian tanpa melanggar ONLY_FULL_GROUP_BY
        $getDailyData = function ($type, $excludeModal = false) use ($startDate, $endDate) {
            $query = CashFlow::query()
                ->selectRaw("DATE(created_at) as date_label, SUM(amount) as total")
                ->where('type', $type)
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($excludeModal) {
                $query->whereDoesntHave('financialCategory', function ($q) {
                    $q->where('slug', 'modal-awal');
                });
            }

            return $query->groupBy('date_label') // Grouping berdasarkan alias yang konsisten
                ->orderBy('date_label')
                ->get()
                ->pluck('total', 'date_label');
        };

        $incomes = $getDailyData('debit', true);
        $expenses = $getDailyData('credit');

        // Generate label tanggal buat sumbu X (biar gak ada tanggal yang bolong)
        $labels = [];
        $incomeValues = [];
        $expenseValues = [];
        
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $incomeValues[] = $incomes[$dateStr] ?? 0;
            $expenseValues[] = $expenses[$dateStr] ?? 0;
            $current->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan (Omzet)',
                    'data' => $incomeValues,
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseValues,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
