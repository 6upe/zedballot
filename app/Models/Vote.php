<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'category_id',
        'nominee_id',
        'voter_id',
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

    public function nominee()
    {
        return $this->belongsTo(Nominee::class);
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}
