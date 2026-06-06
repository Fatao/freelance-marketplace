<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'author_id', 'complainable_id', 'complainable_type',
        'reason', 'status', 'reviewed_by', 'resolution',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Polymorphic — can be Order or User
    public function complainable()
    {
        return $this->morphTo();
    }
}