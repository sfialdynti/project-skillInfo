<?php

namespace App\Http\Controllers;

use App\Models\Competency_standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetencyElementController extends Controller
{
    public function showCE(Request $request)
    {
        $data['profile'] = Auth::user();
        // $csID = $request->input('competency_standards_id', $request->id);
        // $competencyStandard = Competency_standard::with('competency_elements')->find($csID);
        $competencyStandardId = $request->input('competency_standards_id', $request->id);
        $competencyStandard = Competency_standard::with('competency_elements')->findOrFail($competencyStandardId);
        $allCompetencyStandards = Competency_standard::all();

        return view('competency.elements',[
            'cs' => $competencyStandard,
            'element' => $competencyStandard->competency_elements,
            'all' => $allCompetencyStandards
        ]);
    }
}
