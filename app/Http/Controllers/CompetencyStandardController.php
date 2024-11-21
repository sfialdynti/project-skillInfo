<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\Competency_standard;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompetencyStandardController extends Controller
{
    public function showCS()
    {
        $data['profile'] = Auth::user();
        $userId = Auth::id(); 
        $assessor = Assessor::where('users_id', $userId)->first();
        $data['cs'] = Competency_standard::where('assessors_id', $assessor->id)->orderby('unit_code', 'asc')->paginate(10);
        // $data['cs'] = Competency_standard::orderby('unit_code', 'asc')->get();
        // $data['cs'] = Competency_standard::paginate(10);
        return view('assessor.table-competency_standard', $data);
    }

    public function createCS()
    {
        $data['profile'] = Auth::user();
        // $data['assessor'] = Auth::user();
        $data['major'] = Major::all();
        return view('assessor.competency_standard-create', $data);
    }

    public function addCS(Request $request)
    {
        $assessor_id = Assessor::where('users_id', Auth::user()->id)->value('id');

        $request->validate([
            'unit_code' => ['required', 'max:32'],
            'unit_title' => ['required', 'max:64'],
            'unit_description' => 'required',
            'majors_id' => 'required',
        ], [
            'unit_code.required' => 'Unit code cannot be empty',
            'unit_code.max' => 'Maximum 32 characters',
            'unit_title.required' => 'Unit title cannot be empty',
            'unit_title.max' => 'Maximum 64 characters',
            'unit_description.required' => 'Unit description cannot be empty',
            'majors_id.required' => 'Majors cannot be empty'
        ]);

        $cs = Competency_standard::create([
            'unit_code' => $request->unit_code,
            'unit_title' => $request->unit_title,
            'unit_description' => $request->unit_description,
            'majors_id' => $request->majors_id,
            'assessors_id' => $assessor_id
        ]);


        if ($cs) {
            Session::flash('message', 'Data saved successfully');
        } else {
            Session::flash('message', 'Data failed to save');
        }

        return redirect('table-competency_standard');
    }
}
