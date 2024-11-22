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
    public function show()
    {
        $data['profile'] = Auth::user();
        $userId = Auth::id(); 
        $assessor = Assessor::where('users_id', $userId)->first();
        $data['cs'] = Competency_standard::where('assessors_id', $assessor->id)->orderby('unit_code', 'asc')->paginate(10);

        return view('assessor.table-competency_standard', $data);
    }

    public function search(Request $request)
    {
        $data['profile'] = Auth::user();
        $userId = Auth::id();
        $assessor = Assessor::where('users_id', $userId)->first();

        $search = $request->input('search');
        $query = Competency_standard::where('assessors_id', $assessor->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('unit_code', 'LIKE', '%' . $search . '%')
                  ->orWhere('unit_title', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('majors', function ($q) use ($search) {
                    $q->where('major_name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        $data['cs'] = $query->orderby('unit_code', 'asc')->paginate(10)->appends(['search' => $search]);
        return view('assessor.table-competency_standard', $data);
    }

    public function create()
    {
        $data['profile'] = Auth::user();
        $data['major'] = Major::all();

        return view('assessor.competency_standard-create', $data);
    }

    public function add(Request $request)
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

    public function edit(Request $request)
    {
        $data['profile'] = Auth::user();
        $data['cs'] = Competency_standard::find($request->id);
        $data['major'] = Major::all();

        return view('assessor.competency_standard-edit', $data);
    }

    public function update(Request $request)
    {
        Competency_standard::findOrFail($request->id);
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

        $update = Competency_standard::where('id', $request->id)->update([
            'unit_code' => $request->unit_code,
            'unit_title' => $request->unit_title,
            'unit_description' => $request->unit_description,
            'majors_id' => $request->majors_id,
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-competency_standard');
    }

    public function delete(Request $request)
    {
        Competency_standard::find($request->id);
        $delete = Competency_standard::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        }else{
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('table-competency_standard');
    }

    // ADMIN
    public function showCS()
    {
        $data['profile'] = Auth::user();
        $data['cs'] = Competency_standard::orderby('unit_code', 'asc')->get();

        return view('table-competency_standard', $data);
    }

    public function searchCS(Request $request)
    {

    }

    public function createCS()
    {
        $data['profile'] = Auth::user();
        $data['major'] = Major::all();
        $data['assessor'] = Assessor::all();

        return view('admin.competency_standard-create', $data);
    }

    public function addCS(Request $request)
    {
        Competency_standard::findOrFail($request->id);
        $request->validate([
            'unit_code' => ['required', 'max:32', 'unique:competency_standards,unit_code'],
            'unit_title' => ['required', 'max:64'],
            'unit_description' => 'required',
            'majors_id' => 'required',
        ], [
            'unit_code.required' => 'Unit code cannot be empty',
            'unit_code.max' => 'Maximum 32 characters',
            'unit_code.unique' => 'This unit code is already taken',
            'unit_title.required' => 'Unit title cannot be empty',
            'unit_title.max' => 'Maximum 64 characters',
            'unit_description.required' => 'Unit description cannot be empty',
            'majors_id.required' => 'Majors cannot be empty'
        ]);

        $update = Competency_standard::where('id', $request->id)->update([
            'unit_code' => $request->unit_code,
            'unit_title' => $request->unit_title,
            'unit_description' => $request->unit_description,
            'majors_id' => $request->majors_id,
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-competency_standard');
    }
}
