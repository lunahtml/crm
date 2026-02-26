<?php
//app/Filament/Widgets/TasksChart.php
namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksChart extends ChartWidget
{
    protected static ?string $heading = 'Tasks by status';

    protected function getData(): array
    {
        $todo = Task::where('status', 'todo')->count();
        $inProgress = Task::where('status', 'in_progress')->count();
        $review = Task::where('status', 'review')->count();
        $completed = Task::where('status', 'completed')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data' => [$todo, $inProgress, $review, $completed],
                    'backgroundColor' => ['#94a3b8', '#f59e0b', '#3b82f6', '#22c55e'],
                    'borderColor' => ['#64748b', '#b45309', '#1d4ed8', '#16a34a'],
                ],
            ],
            'labels' => ['To Do', 'In Progress', 'Review', 'Completed'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}