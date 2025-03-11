<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $fillable = [
        'title',
        'content',
        'status',
        'scheduled_at',
        'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class)
            ->withPivot(['sent', 'opened', 'sent_at', 'opened_at'])
            ->withTimestamps();
    }
} 