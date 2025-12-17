<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voter extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'identifier_type',
        'identifier_value',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /* =======================
     | Relationships
     ======================= */

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /* =======================
     | Helpers
     ======================= */

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
}
