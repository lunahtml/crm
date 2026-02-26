<?php
//app/Filament/Widgets/HoursPerProject.php
namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class HoursPerProject extends ChartWidget
{
    protected static ?string $heading = 'Hours logged per project (last 7 days)';

    protected function getData(): array
    {
        $projects = Project::with('tasks.timeEntries')
            ->whereHas('tasks.timeEntries', function ($query) {
                $query->where('date', '>=', now()->subDays(7));
            })
            ->get();

        $labels = $projects->pluck('name')->toArray();
        $data = $projects->map(function ($project) {
            return round($project->tasks->sum(function ($task) {
                return $task->timeEntries->sum('hours');
            }), 1);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Hours',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
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