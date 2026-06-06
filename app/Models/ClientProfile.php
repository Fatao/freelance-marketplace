<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'description',
        'phone', 'telegram', 'website',
        'contact_verified', 'rating', 'reviews_count',
    ];

    protected $casts = [
        'contact_verified' => 'boolean',
        'rating'           => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}