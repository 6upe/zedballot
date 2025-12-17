<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EligibleVoter extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'email',
        'phone',
        'name',
        'identifier_type',
        'identifier_value',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /* =======================
     | Relationships
     ======================= */

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /* =======================
     | Helpers
     ======================= */

    public function isRegistered(): bool
    {
        return !is_null($this->registered_at);
    }
}
