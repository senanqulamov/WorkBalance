<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'supplier_id',
        'status',
        'sent_at',
        'responded_at',
        'notes',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the request for this invitation.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the supplier (user) for this invitation.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the quotes submitted for this invitation.
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class, 'supplier_invitation_id');
    }
}
