<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $fillable = [
        'user_id', 'name', 'categories', 'keywords',
        'skills', 'budget_min', 'budget_max', 'source',
    ];

    protected $casts = [
        'categories' => 'array',
        'skills'     => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a given Order matches this saved search criteria.
     */
    public function matchesOrder(Order $order): bool
    {
        if ($this->categories && !in_array($order->category_id, $this->categories)) {
            return false;
        }

        if ($this->keywords) {
            $kw = strtolower($this->keywords);
            if (!str_contains(strtolower($order->title), $kw) &&
                !str_contains(strtolower($order->description), $kw)) {
                return false;
            }
        }

        if ($this->budget_min && $order->budget_max < $this->budget_min) {
            return false;
        }

        if ($this->budget_max && $order->budget_min > $this->budget_max) {
            return false;
        }

        return true;
    }
}