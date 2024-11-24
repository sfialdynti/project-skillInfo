<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class, 'students_id');
    }

    public function majors()
    {
        return $this->belongsTo(Major::class);
    }
}
