<?php

namespace App\Http\Controllers;

use App\Models\Competency_element;
use App\Models\Competency_standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompetencyElementController extends Controller
{
    public function show($id)
    {
        // $csID = $request->input('competency_standards_id', $request->id);
        // $competencyStandardId = $request->input('competency_standard_id', $request->id);
        // $competencyStandard = Competency_standard::with('competency_elements')->findOrFail($competencyStandardId);
        $profile = Auth::user();
        // $competencyStandard = Competency_standard::with('competency_elements')->find($id);
        $all = Competency_standard::all();
        $cs = Competency_standard::findOrFail($id);
        $ce = Competency_element::where('competency_standards_id', $id)->get();


        // return view('assessor.table-competency_element',[
        //     'cs' => $competencyStandard,
        //     'element' => $competencyStandard->competency_elements,
            // 'all' => $allCompetencyStandards,
        //     'profile' => $profile
        // ]);
        return view('assessor.table-competency_element', compact('cs', 'ce', 'profile', 'all'));
    }

    public function create($competency_standards_id)
    {
        $profile = Auth::user();
        $competencyStandard = Competency_standard::findOrFail($competency_standards_id);

        return view('assessor.competency_element-create', compact('competencyStandard', 'profile', 'competency_standards_id'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'competency_standards_id' => ['required', 'exists:competency_standards,id',],
            'criteria' => 'required'
        ], [
            'competency_standards_id.required' => 'Competency standard cannot be empty',
            'competency_standards_id.exists' => 'The Competency standard does not exist',
            'criteria.required' => 'Criteria cannot be empty'
        ]);

        $ce = Competency_element::create([
            'competency_standards_id' => $request->competency_standards_id,
            'criteria' => $request->criteria
        ]);

        if ($ce) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect()->route('competency.elements', ['id' => $request->competency_standards_id]);
    }
}
