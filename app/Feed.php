<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'apikey', 'aspect_id', 'params'
    ];

    public function boards()
    {
        return $this->belongsToMany(Board::class);
    }

    public function aspect()
    {
        return $this->belongsTo(Aspect::class);
    }

    public function scopeForAspect($query, $aspectId)
    {
        return $query->where('aspect_id', $aspectId);
    }
}
