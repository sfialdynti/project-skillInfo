<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessor extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function competency_standards()
    {
        return $this->hasMany(Competency_standard::class);
    }
}
