<?php

namespace App\Livewire\Client\Reports;

use App\Models\ClientAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Reports | Optima FM')]
class ReportsIndex extends Component
{
    public ClientAccount $clientAccount;

    public function mount(): void
    {
        $this->authorize('view reports');
        $this->clientAccount = app(ClientAccount::class);
    }

    public function getReportCategories(): array
    {
        return [
            'work_orders' => [
                'title' => 'Work Order Reports',
                'description' => 'Analyze work order performance, status, and technician metrics',
                'icon' => 'clipboard-document-list',
                'color' => 'blue',
                'reports' => [
                    [
                        'name' => 'Status Distribution',
                        'description' => 'Work order breakdown by status and priority',
                        'route' => 'app.reports.work-orders.status',
                        'icon' => 'chart-pie',
                    ],
                    [
                        'name' => 'SLA Compliance',
                        'description' => 'Response and resolution SLA performance',
                        'route' => 'app.reports.work-orders.sla',
                        'icon' => 'clock',
                    ],
                    [
                        'name' => 'Technician Performance',
                        'description' => 'Workload and completion metrics per technician',
                        'route' => 'app.reports.work-orders.technicians',
                        'icon' => 'user-group',
                    ],
                ],
            ],
            'facilities' => [
                'title' => 'Facility Reports',
                'description' => 'Facility maintenance history and asset condition tracking',
                'icon' => 'building-office-2',
                'color' => 'teal',
                'reports' => [
                    [
                        'name' => 'Maintenance History',
                        'description' => 'Work order history and costs per facility',
                        'route' => 'app.reports.facilities.maintenance',
                        'icon' => 'wrench-screwdriver',
                    ],
                    [
                        'name' => 'Asset Condition',
                        'description' => 'Asset maintenance status and costs',
                        'route' => 'app.reports.facilities.assets',
                        'icon' => 'cube',
                    ],
                ],
            ],
            'financial' => [
                'title' => 'Financial Reports',
                'description' => 'Cost analysis and spending breakdowns',
                'icon' => 'currency-dollar',
                'color' => 'emerald',
                'reports' => [
                    [
                        'name' => 'Cost Summary',
                        'description' => 'Overall cost breakdown and trends',
                        'route' => 'app.reports.financial.costs',
                        'icon' => 'banknotes',
                    ],
                ],
            ],
        ];
    }

    public function render()
    {
       return view('livewire.client.reports.index', [
            'categories' => $this->getReportCategories()
        ]);
    }
}
