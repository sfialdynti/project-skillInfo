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
        $assessor = $profile->assessors->first();

        $students = Student::whereHas('examinations', function ($query) use ($assessor) {
            $query->where('assessors_id', $assessor->id);
        })
        ->with(['examinations' => function ($query) use ($assessor) {
            $query->where('assessors_id', $assessor->id)
                  ->with('competency_elements');
        }])
        ->paginate(10);
        
        return view('assessor.table-assessment', compact('students', 'profile'));
    }

    public function assessStudent($studentId)
    {
        $profile = Auth::user();
        $student = Student::findOrFail($studentId);
        $examinations = Examination::where('students_id', $studentId)->with('competency_elements')->get();
        $ce = $student->examinations;
        $ce = $student->competency_elements;

        return view('assessor.assessment', compact('student', 'examinations', 'profile'));
    }

    public function submitAssessment(Request $request, $studentId)
    {
        $students = Student::with('examinations.competency_elements')->findOrFail($studentId);
        $assessor = Assessor::where('users_id', auth()->id())->first();
        $examDate = $students->examinations->first()->exam_date ?? null;

        foreach ($request->competency_elements as $competencyElementId => $status) {
            $assessment = Examination::updateOrCreate(
                [
                    'students_id' => $students->id,
                    'competency_elements_id' => $competencyElementId,
                    'assessors_id' => $assessor->id
                ], [
                    'exam_date' => $examDate,
                    'status' => $status,
                    'comments' => $request->comments[$competencyElementId] ?? null,
                ]
            );
        }

        $totalCompetency = $students->examinations()->where('status', 1)->count();
        $totalCompetencyElements = $students->examinations()->count();

        $finalScore = ($totalCompetency / $totalCompetencyElements) * 100;
        $evaluationStatus = $this->getEvaluationStatus($finalScore);

        $students->evaluation_status = $evaluationStatus;
        $students->save();

        // dd($totalCompetency, $totalCompetencyElements, $finalScore);

        return redirect()->route('assessor.table-assessment');

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

}
