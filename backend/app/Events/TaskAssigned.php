<?php

namespace App\Events;

use App\Models\Task;
use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
        
        // Создаем запись в БД
        $notification = Notification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Events\\TaskAssigned',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $task->assignee_id,
            'data' => [
                'task_id' => $task->id,
                'title' => $task->title,
                'project' => $task->project?->name,
                'url' => "/admin/tasks/{$task->id}/edit"
            ]
        ]);
        
        Log::info('TaskAssigned event created', [
            'task_id' => $task->id,
            'assignee_id' => $task->assignee_id,
            'notification_id' => $notification->id
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->task->assignee_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->task->id,
            'title' => $this->task->title,
            'project' => $this->task->project?->name,
            'url' => '/admin/tasks/' . $this->task->id . '/edit',
            'assigned_at' => now()->toDateTimeString()
        ];
    }
}