<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'category_id', 'title', 'description',
        'budget_min', 'budget_max', 'payment_format', 'deadline',
        'files', 'links', 'status', 'rejection_reason', 'published_at',
    ];

    protected $casts = [
        'files'        => 'array',
        'deadline'     => 'date',
        'published_at' => 'datetime',
        'budget_min'   => 'decimal:2',
        'budget_max'   => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT         = 'draft';
    const STATUS_ON_MODERATION = 'on_moderation';
    const STATUS_PUBLISHED     = 'published';
    const STATUS_IN_PROGRESS   = 'in_progress';
    const STATUS_COMPLETED     = 'completed';
    const STATUS_CANCELLED     = 'cancelled';
    const STATUS_REJECTED      = 'rejected';

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'order_skills');
    }

    public function applications()
    {
        return $this->hasMany(OrderApplication::class);
    }

    public function acceptedApplication()
    {
        return $this->hasOne(OrderApplication::class)->where('status', 'accepted');
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function work()
    {
        return $this->hasOne(OrderWork::class);
    }

    public function messages()
    {
        return $this->hasMany(OrderMessage::class)->orderBy('created_at');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complainable');
    }

    // --- Helpers ---
    public function isPublished(): bool    { return $this->status === self::STATUS_PUBLISHED; }
    public function isInProgress(): bool   { return $this->status === self::STATUS_IN_PROGRESS; }
    public function isCompleted(): bool    { return $this->status === self::STATUS_COMPLETED; }
    public function isDraft(): bool        { return $this->status === self::STATUS_DRAFT; }
}