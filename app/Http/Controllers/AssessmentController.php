<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\Examination;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    // public function listStudent()
    // {
    //     $profile = Auth::user();
    //     $students = Student::whereHas('examinations')->with(['examinations' => function ($query) {
    //         $query->with('competency_elements');
    //     }])->get();

    //     foreach ($students as $student) {
    //         $examinations = $student->examinations;
    //         $totalElements = $examinations->count();
    //         $competentCount = $examinations->where('status', 1)->count();
    
    //         if ($totalElements > 0) {
    //             $score = ($competentCount / $totalElements) * 100;
    
    //             if ($score >= 91) {
    //                 $student->evaluation_status = 'Sangat Kompeten';
    //             } elseif ($score >= 75 && $score <= 91) {
    //                 $student->evaluation_status = 'Kompeten';
    //             } elseif ($score >= 61  && $score <= 75) {
    //                 $student->evaluation_status = 'Cukup Kompeten';
    //             } else {
    //                 $student->evaluation_status = 'Belum Kompeten';
    //             }
    //         } else {
    //             $student->evaluation_status = 'Belum Dinilai';
    //         }
    //     }

    //     return view('assessor.table-assessment', compact('students', 'profile'));
    // }

    public function listStudent()
    {
        $profile = Auth::user();
        $examinations = Examination::where('assessors_id', auth()->user()->id)->get();
        $students = Student::whereHas('examinations')->with(['examinations' => function ($query) {
                    $query->with('competency_elements');
                }])->get();
        
        return view('assessor.table-assessment', compact('students', 'profile'));
    }

    public function assessStudent($studentId)
    {
        $profile = Auth::user();
        $student = Student::findOrFail($studentId);
        $examinations = Examination::where('students_id', $studentId)->with('competency_elements')->get();
        $ce = $student->examinations;

        return view('assessor.assessment', compact('student', 'examinations', 'profile', 'ce'));
    }

    public function submitAssessment(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $assessor = Assessor::where('users_id', auth()->id())->first();
        $examDate = $student->examinations->first()->exam_date ?? null;
        foreach ($request->competency as $competencyElementId => $status) {
            $assessment = new Examination();
            $assessment->exam_date = $examDate;
            $assessment->students_id = $student->id;
            $assessment->assessors_id = $assessor->id;  
            $assessment->competency_elements_id = $competencyElementId;
            $assessment->status = $status;
            $assessment->comments = $request->comments[$competencyElementId] ?? null;
            $assessment->save();
        }

        $totalCompetency = $student->examinations()->where('status', 1)->count();
        $totalCompetencyElements = $student->competencyElements->count();

        $finalScore = ($totalCompetency / $totalCompetencyElements) * 100;
        $evaluationStatus = $this->getEvaluationStatus($finalScore);

        $student->evaluation_status = $evaluationStatus;
        $student->save();

        return redirect()->route('/liststudent');

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

    // public function submitAssessment(Request $request, $studentId)
    // {
    //     $validate = $request->validate([
    //         'status' => ['required', 'array'],
    //         'status.*' => ['required', 'in:0,1'],
    //         'comments' => ['nullable', 'array']
    //     ]);

    //     $student = Student::findOrFail($studentId);

    //     foreach ($request->status as $elementId => $status) {
    //         $exam = Examination::where('students_id', $student->id)
    //             ->whereHas('competency_elements', function ($query) use ($elementId) {
    //                 $query->where('competency_elements.id', $elementId);
    //             })
    //             ->first();
    
    //         // if ($exam) {
    //         //     $exam->status = $status;
    //         //     $exam->comment = $request->comment[$elementId] ?? '';
    //         //     $exam->save();
    //         // }
    //         if ($exam) {
    //             // Update status dan comment untuk examination yang sudah ada
    //             $exam->update([
    //                 'status' => $status,  // Update status
    //                 'comment' => $request->comments[$elementId] ?? '',  // Update comment jika ada
    //             ]);
    //         }
    //     }

    //     $totalElements = $student->examinations->count();
    //     $competentCount = $student->examinations->where('status', 1)->count();
        
    //     if ($totalElements > 0) {
    //         $score = ($competentCount / $totalElements) * 100;

    //         if ($score >= 91) {
    //             $student->evaluation_status = 'Sangat Kompeten';
    //         } elseif ($score >= 75) {
    //             $student->evaluation_status = 'Kompeten';
    //         } elseif ($score >= 61) {
    //             $student->evaluation_status = 'Cukup Kompeten';
    //         } else {
    //             $student->evaluation_status = 'Belum Kompeten';
    //         }
    //     } else {
    //         $student->evaluation_status = 'Belum Dinilai';
    //     }

    //     $student->save();

    //     return redirect()->route('assessor.table-assessment');
    // }

}
