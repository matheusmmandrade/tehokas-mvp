<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    protected $appends = ['health_status'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getHealthStatusAttribute(): string
    {
        if ($this->tasks->isEmpty()) {
            return 'Saudável';
        }

        $totalTasks = $this->tasks->count();

        $overdueTasks = $this->tasks
            ->where('status', '!=', 'completed')
            ->where('deadline', '<', now())
            ->count();

        $percentage = ($overdueTasks / $totalTasks) * 100;

        return $percentage > 20 ? 'Em Alerta' : 'Saudável';
    }
}