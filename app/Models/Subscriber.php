<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model
{
    use Notifiable;

    protected $fillable = [
        'email',
        'user_id',
        'is_active',
        'status',
        'confirmation_token',
        'confirmed_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'confirmed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newsletters()
    {
        return $this->belongsToMany(Newsletter::class)
            ->withPivot(['sent', 'opened', 'sent_at', 'opened_at'])
            ->withTimestamps();
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }
} 