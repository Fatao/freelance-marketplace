<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderWork extends Model
{

    protected $table = 'order_work';
    protected $fillable = [
        'order_id', 'freelancer_id', 'status', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    const STATUS_IN_PROGRESS     = 'in_progress';
    const STATUS_ON_REVIEW       = 'on_review';
    const STATUS_NEEDS_REVISION  = 'needs_revision';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_CANCELLED       = 'cancelled';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}