<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
        'department_id',
        'category',
        'title',
        'text',
        'priority',
        'generated_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function isPending(): bool
    {
        return $this->acknowledged_at === null;
    }

    public function acknowledge(): void
    {
        $this->update(['acknowledged_at' => now()]);
    }
}
