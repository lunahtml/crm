<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'status', 
        'hours_estimated', 
        'project_id', 
        'assignee_id',
        'epic_id'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function epic()
    {
        return $this->belongsTo(Epic::class);
    }
    public function scopeAvailableForEpic($query, $epicId, $projectId)
{
    return $query->where('project_id', $projectId)
        ->where(function ($q) use ($epicId) {
            $q->whereNull('epic_id')
              ->orWhere('epic_id', $epicId);
        });
}
}