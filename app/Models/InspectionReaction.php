<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_inspection_id',
        'estate_configuration_id',
        'comment',
        'photo'
    ];

    public function propertyInspection()
    {
        return $this->belongsTo(PropertyInspection::class);
    }

    public function estateConfiguration()
    {
        return $this->belongsTo(EstateConfiguration::class);
    }
}
