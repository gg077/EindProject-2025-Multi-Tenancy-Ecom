<?php

namespace App\Livewire\SuperAdmin\Dashboard;

use Livewire\Component;
use App\Models\Tenant;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    public $selectedYear;

    public function mount()
    {
        // Stel het geselecteerde jaar in op het huidige jaar bij laden van de component
        $this->selectedYear = now()->year;
    }

    // haal alle gegevens op die nodig zijn voor de dashboardweergave
    public function getDashboardData()
    {
        // 1. Algemene statistieken over tenants
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'inactive' => Tenant::where('status', '!=', 'active')->count(),
        ];
        
        // 2. Data voor grafiek: aantal geregistreerde tenants per maand in geselecteerd jaar
        $chartData = Tenant::query()
            ->whereYear('created_at', $this->selectedYear) // Filter alleen tenants van geselecteerd jaar
            ->select(
                DB::raw('MONTH(created_at) as month'), // Haal maandnummer uit created_at
                DB::raw('COUNT(*) as tenant_count')) // Aantal tenants per maand
            ->groupBy('month') // Groepeer per maand
            ->orderBy('month') // Sorteer oplopend per maand
            ->get();
            
        // 3. Maak een lijn-grafiek model aan met titel en animatie
        $chart = (new LineChartModel())
            ->setTitle('Tenants Registered per Month')
            ->setAnimated(true)
            ->withOnPointClickEvent('onPointClick');
            
        //  4. Voeg data-punten toe aan de grafiek (voor elke maand van het jaar)
        for ($month = 1; $month <= 12; $month++) {
            $chart->addPoint(
                Carbon::create()->month($month)->format('M'), 
                $chartData->firstWhere('month', $month)->tenant_count ?? 0
            );
        }
        // 5. Retourneer zowel de statistieken als het grafiekmodel aan de view
        return [
            'tenantStats' => $stats,
            'tenantsPerMonthChart' => $chart
        ];
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard.analytics', $this->getDashboardData());
    }
}
