<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivacyAuditLog extends Model
{
    protected $table = 'privacy_audit_log';

    protected $fillable = [
        'export_id',
        'rules_applied',
        'min_group_size',
        'actual_group_size',
        'delay_hours',
        'passed',
        'failure_reason',
    ];

    protected $casts = [
        'rules_applied' => 'array',
        'passed' => 'boolean',
    ];

    public function export()
    {
        return $this->belongsTo(AggregationExport::class, 'export_id');
    }
}
