<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function students()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function assessors()
    {
        return $this->belongsTo(Assessor::class);
    }

    public function competency_elements()
    {
        return $this->belongsTo(Competency_element::class, 'competency_elements_id');
    }

}
