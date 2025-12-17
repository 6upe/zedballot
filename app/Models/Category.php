<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'name',
        'description',
    ];

    /* =======================
     | Relationships
     ======================= */

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function nominees()
    {
        return $this->hasMany(Nominee::class);
    }
}
