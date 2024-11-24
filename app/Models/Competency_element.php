<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competency_element extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function examinations()
    {
        return $this->hasMany(Examination::class, 'competency_elements_id');
    }

    public function competency_standards()
    {
        return $this->belongsTo(Competency_standard::class, 'competency_standards_id');
    }
}
