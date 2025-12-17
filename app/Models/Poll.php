<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'start_at',
        'end_at',
        'status',
        'cover_image',
        'banner_image',
        'video',
        'voting_methods',
        'is_public',
        'email_domain',
        'country',
        'allow_vote_edit',
        'created_by',
        'nominee_registration_token',
        'voter_registration_token',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_public' => 'boolean',
        'allow_vote_edit' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted()
    {
        static::creating(function ($poll) {
            $poll->uuid ??= Str::uuid();
        });
    }

    /* =======================
     | Relationships
     ======================= */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function nominees()
    {
        return $this->hasMany(Nominee::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function eligibleVoters()
    {
        return $this->hasMany(EligibleVoter::class);
    }

    /* =======================
     | Helpers
     ======================= */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_at, $this->end_at);
    }

    public function getVotingMethods(): array
    {
        return $this->voting_methods
            ? explode(',', $this->voting_methods)
            : [];
    }

    public function generateNomineeRegistrationToken(): string
    {
        if (!$this->nominee_registration_token) {
            $this->nominee_registration_token = Str::random(64);
            $this->save();
        }
        return $this->nominee_registration_token;
    }

    public function generateVoterRegistrationToken(): string
    {
        if (!$this->voter_registration_token) {
            $this->voter_registration_token = Str::random(64);
            $this->save();
        }
        return $this->voter_registration_token;
    }
}
