<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\Competency_element;
use App\Models\Examination;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExaminationController extends Controller
{
    public function show()
    {
        $data['profile'] = Auth::user();
        $data['student'] = Student::all();
        $userId = Auth::id();
        $assessor = Assessor::where('users_id', $userId)->first();
        // $data['ce'] = Competency_element::where('assessors_id', $assessor->id);
        $data['ce'] = Competency_element::whereHas('competency_standards', function ($query) use ($assessor) {
            $query->where('assessors_id', $assessor->id);
        })->with('examinations')->get();

        $data['exam'] = Examination::orderby('exam_date', 'asc')->get();
        $data['exam'] = Examination::paginate(10);

        return view('assessor.table-exam', $data);

    }

    public function create()
    {

    }

}
