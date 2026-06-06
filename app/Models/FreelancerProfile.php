<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    protected $fillable = [
        'user_id', 'display_name', 'specialization', 'experience',
        'portfolio', 'hourly_rate', 'phone', 'telegram', 'website',
        'rating', 'reviews_count', 'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'hourly_rate'  => 'decimal:2',
        'rating'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'freelancer_skills');
    }
}