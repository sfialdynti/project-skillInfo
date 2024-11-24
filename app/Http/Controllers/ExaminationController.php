<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\Competency_element;
use App\Models\Examination;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

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
        $profile = Auth::user();
        $student = Student::all();
        $ce = Competency_element::all();

        return view('assessor.exam-create', compact('student', 'ce', 'profile'));
    }

    public function add(Request $request)
    {
       $validate = $request->validate([
            'exam_date' => ['required', 'date'],
            'students_id' => ['required', 'exists:students,id'],
            'competency_elements_id' => ['required', 'array'],
            'competency_elements_id.*' => 'exists:competency_elements,id',
        ], [
            'exam_date.required' => 'Exam date cannot be empty',
            'exam_date.date' => 'Exam date must be date',
            'students_id.required' => 'Student cannot be empty',
            'students_id.exists' =>  'The student does not exist',
            'competency_elements_id.array' => 'Competency element cannot be empty',
            'competency_elements_id.required' => 'Competency element cannot be empty',
            'competency_elements_id.exists' => 'The competency element does not exist'
        ]);

        // dd($validate);

        // Log::info('Data yang diterima:', $validate);
        $userId = Auth::id();
        $assessor = Assessor::where('users_id', $userId)->first();
        foreach ($validate['competency_elements_id'] as $elementId) {
            Examination::create([
                'exam_date' => $validate['exam_date'],
                'students_id' => $validate['students_id'],
                'competency_elements_id' => $elementId,
                'assessors_id' => $assessor->id,
            ]);
        }
        
        return redirect('table-exam');
    
    }

    public function edit(Request $request)
    {
        $profile = Auth::user();
        $exam = Examination::find($request->id);
        $student = Student::all();
        $ce = Competency_element::all(); 
        $selectedElements = $exam->competency_elements_id ? json_decode($exam->competency_elements_id, true) : [];
        // $selectedElements = json_decode($exam->competency_elements_id, true);

        return view('assessor.exam-edit', [
            'exam' => $exam,
            'student' => $student,
            'ce' => $ce,
            'selectedElements' => $selectedElements,
            'profile' => $profile
        ]);
    }

    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'exam_date' => ['required', 'date'],
            'students_id' => ['required', 'exists:students,id'],
            'competency_elements_id' => ['required', 'array'],
            'competency_elements_id.*' => 'exists:competency_elements,id',
        ], [
            'exam_date.required' => 'Exam date cannot be empty',
            'exam_date.date' => 'Exam date must be date',
            'students_id.required' => 'Student cannot be empty',
            'students_id.exists' =>  'The student does not exist',
            'competency_elements_id.array' => 'Competency element cannot be empty',
            'competency_elements_id.required' => 'Competency element cannot be empty',
            'competency_elements_id.exists' => 'The competency element does not exist'
        ]);
    
        $exam = Examination::findOrFail($id);

        $update = Examination::where('id', $request->id)->update([
            'exam_date' => $validate['exam_date'],
            'students_id' => $validate['students_id'],
            'competency_elements_id' => json_encode($validate['competency_elements_id']),
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('/table-exam');
    }

    public function delete(Request $request)
    {
        Examination::find($request->id);
        $delete = Examination::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        }else{
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('/table-exam');
    }

}
