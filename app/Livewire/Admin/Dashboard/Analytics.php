<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Order;
use App\Models\User;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Carbon\Carbon; // datumbewerking laravel
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    public $selectedYear; // Jaarfilter (gebonden aan dropdown in Blade)

    public function mount()
    {
        // Zet standaard geselecteerd jaar op het huidige jaar
        $this->selectedYear = now()->year;
    }

    //Chart één
    public function getRevenuePerProductChart()
    {
        $data = Order::query()
            ->where('orders.tenant_id', tenant('id')) // Alleen data van huidige tenant
            ->where('orders.status', Order::STATUS_PAID) // Alleen betaalde bestellingen
            ->whereYear('orders.created_at', $this->selectedYear) // Alleen van geselecteerd jaar
            ->join('order_items', 'orders.id', '=', 'order_items.order_id') // Verbind orderregels
            ->join('products', 'order_items.product_id', '=', 'products.id')  // Verbind producten
            ->select('products.name', DB::raw('SUM(order_items.product_price * order_items.quantity) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10) // Haal top 10 producten op op basis van omzet
            ->get();

        // Start kolomgrafiek met animatie en click-event
        $chart = (new ColumnChartModel())
            ->setTitle('Revenue per Product')
            ->setAnimated(true)
            ->withOnColumnClickEventName('onColumnClick');

        // Voeg elk product toe als kolom met naam en bedrag
        foreach ($data as $item) {
            $chart->addColumn($item->name, number_format($item->total_revenue, 2), '#000000');
        }
        return $chart;
    }

    // Chart twee
    public function getRevenuePerMonthChart()
    {
        $data = Order::query()
            ->where('tenant_id', tenant('id')) // Alleen data van huidige tenant
            ->where('status', Order::STATUS_PAID) // Alleen betaalde bestellingen
            ->whereYear('created_at', $this->selectedYear) // Alleen het geselecteerde jaar
            ->select(
                DB::raw('MONTH(created_at) as month'), // Haal maandnummer op
                DB::raw('SUM(total_amount) as total_revenue') // Totaal Maand omzet
            )
            ->groupBy('month') // Groepeer per maand
            ->orderBy('month') // Sorteer oplopend
            ->get();

        $chart = (new LineChartModel()) // Maak een nieuwe lijngrafiek aan
            ->setTitle('Revenue per Month') // Titel van grafiek
            ->setAnimated(true) // Animatie bij laden
            ->withOnPointClickEvent('onPointClick'); //Event als je klikt op een punt
        
        // voeg alle 12 maanden toe zelfs als er geen omzet is
        for ($month = 1; $month <= 12; $month++) {
            $totalRevenue = $data->firstWhere('month', $month)->total_revenue ?? 0;
            $monthName = Carbon::create()->month($month)->format('M');
            $chart->addPoint($monthName, number_format($totalRevenue, 2));
        }

        return $chart;
    }

    // Tel alle betaalde bestellingen ooit op
    public function getTotalSalesAllTime()
    {
        return Order::where('status', Order::STATUS_PAID)->sum('total_amount');
    }

    // Tel alle niet-admin gebruikers (klanten)
    public function getTotalRegisteredClients()
    {
        return User::where('is_admin', false)->count();
    }

    // Haal de top 3 best verkochte producten op
    public function getTop3BestSoldProducts()
    {
        return Order::query()
            ->where('orders.status', Order::STATUS_PAID)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get();
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard.analytics', [
            'revenuePerProductChart' => $this->getRevenuePerProductChart(),
            'revenuePerMonthChart' => $this->getRevenuePerMonthChart(),
            'totalSalesAllTime' => $this->getTotalSalesAllTime(),
            'totalRegisteredClients' => $this->getTotalRegisteredClients(),
            'top3BestSoldProducts' => $this->getTop3BestSoldProducts(),
        ]);
    }
}
