<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AggregationExport extends Model
{
    protected $fillable = [
        'period',
        'period_start',
        'period_end',
        'department_id',
        'exported_at',
        'status',
        'records_count',
        'min_group_size_met',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'exported_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
