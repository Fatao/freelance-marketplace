<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlerSource extends Model
{
    protected $fillable = [
        'name', 'base_url', 'crawl_rules', 'extract_rules',
        'frequency_minutes', 'status', 'last_run_at',
    ];

    protected $casts = [
        'crawl_rules'   => 'array',
        'extract_rules' => 'array',
        'last_run_at'   => 'datetime',
    ];

    const STATUS_ACTIVE   = 'active';
    const STATUS_DISABLED = 'disabled';
    const STATUS_ERROR    = 'error';

    public function logs()
    {
        return $this->hasMany(CrawlerLog::class);
    }

    public function externalOrders()
    {
        return $this->hasMany(ExternalOrder::class);
    }

    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
}