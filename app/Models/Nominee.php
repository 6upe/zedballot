<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'category_id',
        'name',
        'email',
        'phone',
        'social_link',
        'bio',
        'photo',
        'status',
        'registration_token',
    ];

    /* =======================
     | Relationships
     ======================= */

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /* =======================
     | Helpers
     ======================= */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
