<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlerLog extends Model
{
    protected $fillable = [
        'crawler_source_id', 'started_by', 'trigger',
        'found', 'created', 'updated', 'archived',
        'errors', 'error_details', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(CrawlerSource::class, 'crawler_source_id');
    }

    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }
}