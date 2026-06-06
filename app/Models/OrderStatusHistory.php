<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model

{

    protected $table = 'order_status_history';

    protected $fillable = ['order_id', 'changed_by', 'old_status', 'new_status', 'comment'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}