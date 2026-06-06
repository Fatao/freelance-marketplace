<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderApplication extends Model
{
    protected $fillable = [
        'order_id', 'freelancer_id', 'cover_letter',
        'proposed_price', 'proposed_days', 'status',
    ];

    protected $casts = [
        'proposed_price' => 'decimal:2',
    ];

    const STATUS_SENT      = 'sent';
    const STATUS_VIEWED    = 'viewed';
    const STATUS_ACCEPTED  = 'accepted';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_WITHDRAWN = 'withdrawn';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function isAccepted(): bool { return $this->status === self::STATUS_ACCEPTED; }
}