<?php

namespace App\Http\Controllers;

use App\Models\Competency_standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetencyElementController extends Controller
{
    public function show(Request $request, $id)
    {
        // $csID = $request->input('competency_standards_id', $request->id);
        // $competencyStandardId = $request->input('competency_standard_id', $request->id);
        // $competencyStandard = Competency_standard::with('competency_elements')->findOrFail($competencyStandardId);
        $profile = Auth::user();
        $competencyStandard = Competency_standard::with('competency_elements')->find($id);
        $allCompetencyStandards = Competency_standard::all();

        return view('assessor.table-competency_element',[
            'cs' => $competencyStandard,
            'element' => $competencyStandard->competency_elements,
            'all' => $allCompetencyStandards,
            'profile' => $profile
        ]);
    }
}
