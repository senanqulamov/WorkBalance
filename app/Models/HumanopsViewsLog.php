<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanopsViewsLog extends Model
{
    use HasFactory;

    protected $table = 'humanops_views_log';

    protected $fillable = [
        'user_id',
        'section',
        'department_id',
        'viewed_at',
        'ip_address',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
