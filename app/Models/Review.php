<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['order_id', 'author_id', 'recipient_id', 'rating', 'comment'];

    protected $casts = ['rating' => 'integer'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}