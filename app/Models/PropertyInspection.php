<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_id',
        'user_id',
        'status',
        'who',
        'config',
        'date',
        'comments'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
