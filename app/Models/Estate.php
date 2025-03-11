<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'price',
        'location'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function configurations()
    {
        return $this->hasMany(EstateConfiguration::class);
    }

    public function inspections()
    {
        return $this->hasMany(PropertyInspection::class);
    }
} 