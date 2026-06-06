<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name', 'slug', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_skills');
    }

    public function freelancers()
    {
        return $this->belongsToMany(FreelancerProfile::class, 'freelancer_skills');
    }
}