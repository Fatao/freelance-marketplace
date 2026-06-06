<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_blocked', 'block_reason'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked' => 'boolean',
    ];

    // --- Role helpers ---
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isModerator(): bool  { return $this->role === 'moderator'; }
    public function isClient(): bool     { return $this->role === 'client'; }
    public function isFreelancer(): bool { return $this->role === 'freelancer'; }

    // --- Relationships ---
    public function freelancerProfile()
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function applications()
    {
        return $this->hasMany(OrderApplication::class, 'freelancer_id');
    }

    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'author_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'recipient_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'author_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(OrderMessage::class, 'sender_id');
    }
}