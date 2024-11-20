<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competency_standard extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function competency_elements()
    {
        return $this->hasMany(Competency_element::class);
    }

    public function majors()
    {
        return $this->belongsTo(Major::class);
    }

    public function assessors()
    {
        return $this->belongsTo(Assessor::class);
    }
}
