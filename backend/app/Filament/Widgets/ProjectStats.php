<?php
//app/Filament/Widgets/ProjectStats.php
namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $totalInvoices = Invoice::sum('amount');
        $pendingInvoices = Invoice::where('status', 'pending')->sum('amount');

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('All time')
                ->descriptionIcon('heroicon-m-folder')
                ->color('success'),

            Stat::make('Active Projects', $activeProjects)
                ->description('In progress')
                ->descriptionIcon('heroicon-m-play')
                ->color('warning'),

            Stat::make('Total Tasks', $totalTasks)
                ->description('All time')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Completed Tasks', $completedTasks)
                ->description($totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) . '% done' : '0% done')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Invoiced', '$' . number_format($totalInvoices, 2))
                ->description('All time')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Invoices', '$' . number_format($pendingInvoices, 2))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}