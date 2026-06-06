<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMessage extends Model
{
    protected $fillable = ['order_id', 'sender_id', 'body', 'attachments', 'is_read'];

    protected $casts = [
        'attachments' => 'array',
        'is_read'     => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}