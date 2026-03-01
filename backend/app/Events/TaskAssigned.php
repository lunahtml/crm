<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
        
        Log::info('TaskAssigned event created', [
            'task_id' => $task->id,
            'assignee_id' => $task->assignee_id,
            'title' => $task->title
        ]);
    }

    public function broadcastOn(): array
    {
        $channel = 'user.' . $this->task->assignee_id;
        
        Log::info('TaskAssigned broadcasting on channel', [
            'channel' => $channel,
            'private_channel' => 'private-' . $channel
        ]);
        
        return [
            new PrivateChannel($channel),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.assigned';
    }

    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->task->id,
            'title' => $this->task->title,
            'project' => $this->task->project?->name,
            'url' => '/admin/tasks/' . $this->task->id . '/edit',
            'assigned_at' => now()->toDateTimeString(),
        ];
        
        Log::info('TaskAssigned broadcast data', $data);
        
        return $data;
    }
    
    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        $should = !is_null($this->task->assignee_id);
        
        Log::info('TaskAssigned broadcastWhen', [
            'should_broadcast' => $should,
            'assignee_id' => $this->task->assignee_id
        ]);
        
        return $should;
    }
}