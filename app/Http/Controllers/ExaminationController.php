<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\Competency_element;
use App\Models\Examination;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class ExaminationController extends Controller
{
    public function show()
    {
        // $data['profile'] = Auth::user();
        // $data['student'] = Student::all();
        // $userId = Auth::id();
        // $assessor = Assessor::where('users_id', $userId)->first();
        // // $data['ce'] = Competency_element::where('assessors_id', $assessor->id);
        // $data['ce'] = Competency_element::whereHas('competency_standards', function ($query) use ($assessor) {
        //     $query->where('assessors_id', $assessor->id);
        // })->with('examinations')->get();

        // $data['exam'] = Examination::orderby('exam_date', 'asc')->get();
        // $data['exam'] = Examination::paginate(10);

        // return view('assessor.table-exam', $data);

        $profile = Auth::user();
        $userId = Auth::id();
        $assessor = Assessor::where('users_id', $userId)->first();

        $student = Student::whereHas('examinations', function ($query) use ($assessor) {
            $query->where('assessors_id', $assessor->id);
        })->get();

        $ce = Competency_element::whereHas('competency_standards', function ($query) use ($assessor) {
            $query->where('assessors_id', $assessor->id);
        })->with('examinations')->get();

        $exam = Examination::where('assessors_id', $assessor->id)
                                    ->orderBy('exam_date', 'asc')
                                    ->paginate(10);

        return view('assessor.table-exam', compact('profile', 'student', 'ce', 'exam'));

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

    public function showExam()
    {
        $data['profile'] = Auth::user();
        $data['student'] = Student::all();
        $data['assessor'] = Assessor::all();
        $data['ce'] = Competency_element::all();
        $data['exam'] = Examination::orderby('exam_date', 'asc')->get();
        $data['exam'] = Examination::paginate(10);

        return view('admin.table-exam-adm', $data);
    }

    public function createExam()
    {
        $profile = Auth::user();
        $student = Student::all();
        $ce = Competency_element::all();
        $assessor = Assessor::all();

        return view('admin.exam-adm-create', compact('student', 'ce', 'profile', 'assessor'));
    }

    public function addExam(Request $request)
    {
        $validate = $request->validate([
            'exam_date' => ['required', 'date'],
            'students_id' => ['required', 'exists:students,id'],
            'assessors_id' => 'required',
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

        foreach ($validate['competency_elements_id'] as $elementId) {
            Examination::create([
                'exam_date' => $validate['exam_date'],
                'students_id' => $validate['students_id'],
                'assessors_id' => $validate['assessors_id'],
                'competency_elements_id' => $elementId,
                'status' => null, 
                'comments' => null
            ]);
        }

        return redirect('/table-exam-adm');

    }

    public function editExam(Request $request)
    {
        $profile = Auth::user();
        $exam = Examination::find($request->id);
        $student = Student::all();
        $ce = Competency_element::all(); 
        $selectedElements = $exam->competency_elements_id ? json_decode($exam->competency_elements_id, true) : [];

        return view('admin.exam-adm-edit', [
            'exam' => $exam,
            'student' => $student,
            'ce' => $ce,
            'selectedElements' => $selectedElements,
            'profile' => $profile
        ]);
    }

    public function updateExam(Request $request, $id)
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

        return redirect('/table-exam-adm');
    }

    public function deleteExam(Request $request)
    {
        Examination::find($request->id);
        $delete = Examination::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        }else{
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('/table-exam-adm');
    }

    //RESULT
    public function result()
    {
        $profile = Auth::user();
        $userId = Auth::id();
        $student = Student::where('users_id', $userId)->first();
        $exam = Examination::where('students_id', $student->id)
        ->whereHas('competency_elements')
        ->whereHas('competency_elements.competency_standards')
        ->with(['competency_elements.competency_standards', 'assessors.users'])
        ->get();

        $examgroup = $exam->groupBy(function ($exam) {
            return $exam->competency_elements->competency_standards->id;
        });

        $totalCompetency = $exam->where('status', 1)->count();
        $totalCompetencyElements = $exam->count();

        
        if ($totalCompetencyElements > 0) {
            $finalScore = ($totalCompetency / $totalCompetencyElements) * 100;
        } else {
            $finalScore = 0;
        }

        $status = $this->getEvaluationStatus($finalScore);

        $student->evaluation_status = $status;
        $student->save();

        return view('student.result', compact('profile', 'examgroup', 'student', 'status'));
    }

    private function getEvaluationStatus($score)
    {
        if ($score >= 91) {
            return 'Sangat Kompeten';
        } elseif ($score >= 75) {
            return 'Kompeten';
        } elseif ($score >= 61) {
            return 'Cukup Kompeten';
        } else {
            return 'Belum Kompeten';
        }
    }

    public function print_pdf()
    {   
        $userId = Auth::id();
        $student = Student::where('users_id', $userId)->first();
        $exam = Examination::where('students_id', $student->id)
        ->whereHas('competency_elements')
        ->whereHas('competency_elements.competency_standards')
        ->with(['competency_elements.competency_standards', 'assessors'])
        ->get();

        $examgroup = $exam->groupBy(function ($exam) {
            return $exam->competency_elements->competency_standards->id;
        });

        if ($exam->isEmpty()) {
            return redirect()->route('student.dashboard')->with('message', 'Anda belum memiliki ujian.');
        }

        $totalCompetency = $exam->where('status', 1)->count();
        $totalCompetencyElements = $exam->count();

        if ($totalCompetencyElements > 0) {
            $finalScore = ($totalCompetency / $totalCompetencyElements) * 100;
        } else {
            $finalScore = 0;
        }

        $status = $this->getEvaluationStatus($finalScore);

        $student->evaluation_status = $status;
        $student->save();

        $pdf = FacadePdf::loadView('student.sertifikat', ['examgroup' => $examgroup, 'student' => $student, 'status' => $status]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('exam_results.pdf');
        // return view('student.result', compact('profile'));
    }

}
