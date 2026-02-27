<?php

namespace App\Filament\Widgets;

use App\Models\CashFlow;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyOmzetChart extends ChartWidget
{
    protected ?string $heading = 'Perkembangan Omzet per Bulan';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full'; 
    protected function getData(): array
    {
        
        $startOfPeriod = now()->subMonths(11)->startOfMonth();

        $data = CashFlow::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(amount) as total")
            ->where('type', 'debit')
            ->whereDoesntHave('financialCategory', function ($query) {
                
                $query->where('slug', 'modal-awal'); 
            })
            ->where('created_at', '>=', $startOfPeriod)
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->pluck('total', 'month_key');

        dd($data);

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            
            $labels[] = $month->translatedFormat('M Y'); 
            
            $values[] = (float) ($data[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Omzet Bulanan (IDR)',
                    'data' => $values,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 4, 
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
