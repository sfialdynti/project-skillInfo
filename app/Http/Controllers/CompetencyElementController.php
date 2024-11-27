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
        $profile = Auth::user();
        $all = Competency_standard::all();
        $cs = Competency_standard::findOrFail($id);
        $ce = Competency_element::where('competency_standards_id', $id)->paginate(10);

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

    public function edit(Request $request)
    {
        $data['profile'] = Auth::user();
        $data['ce'] = Competency_element::find($request->id);

        return view('assessor.competency_element-edit', $data);
    }

    public function update(Request $request)
    {
        $ce = Competency_element::findOrFail($request->id);
        $request->validate([
            'criteria' => 'required'
        ], [
            'criteria.required' => 'Criteria cannot be empty'
        ]);

        $update = Competency_element::where('id', $request->id)->update([
            'criteria' => $request->criteria
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect()->route('competency.elements', ['id' => $ce->competency_standards_id]);
    }

    public function delete(Request $request)
    {
        Competency_element::find($request->id);
        $delete = Competency_element::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        }else{
            Session::flash('message', 'Data failed to delete');
        }

        return redirect()->back();
    }

    public function showCE($id)
    {
        $profile = Auth::user();
        $all = Competency_standard::all();
        $cs = Competency_standard::findOrFail($id);
        $ce = Competency_element::where('competency_standards_id', $id)->paginate(10);

        return view('admin.table-competency_element-adm', compact('cs', 'ce', 'profile', 'all'));
    }

    public function createCE($competency_standards_id)
    {
        $profile = Auth::user();
        $competencyStandard = Competency_standard::findOrFail($competency_standards_id);

        return view('admin.competency_element-adm-create', compact('competencyStandard', 'profile', 'competency_standards_id'));
    }

    public function addCE(Request $request)
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

        return redirect()->route('competency.elements-adm', ['id' => $request->competency_standards_id]);
    }

    public function editCE(Request $request)
    {
        $data['profile'] = Auth::user();
        $data['ce'] = Competency_element::find($request->id);

        return view('admin.competency_element-adm-edit', $data);
    }

    public function updateCE(Request $request)
    {
        $ce = Competency_element::findOrFail($request->id);
        $request->validate([
            'criteria' => 'required'
        ], [
            'criteria.required' => 'Criteria cannot be empty'
        ]);

        $update = Competency_element::where('id', $request->id)->update([
            'criteria' => $request->criteria
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect()->route('competency.elements-adm', ['id' => $ce->competency_standards_id]);
    }

    public function deleteCE(Request $request)
    {
        Competency_element::find($request->id);
        $delete = Competency_element::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        }else{
            Session::flash('message', 'Data failed to delete');
        }

        return redirect()->back();
    }

}
