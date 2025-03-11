<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstateConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_id',
        'level',
        'room_type',
        'room_count',
        'additional_info',
        'details',
        'pieces'
    ];

    protected $casts = [
        'details' => 'json',
        'pieces' => 'json'
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }
}
