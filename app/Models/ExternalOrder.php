<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalOrder extends Model
{
    protected $fillable = [
        'crawler_source_id', 'category_id', 'title', 'description',
        'skills', 'budget', 'deadline', 'source_url',
        'status', 'discovered_at', 'last_updated_at',
    ];

    protected $casts = [
        'skills'          => 'array',
        'deadline'        => 'date',
        'discovered_at'   => 'datetime',
        'last_updated_at' => 'datetime',
        'budget'          => 'decimal:2',
    ];

    const STATUS_NEW      = 'new';
    const STATUS_ACTIVE   = 'active';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_ERROR    = 'error';

    public function source()
    {
        return $this->belongsTo(CrawlerSource::class, 'crawler_source_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}