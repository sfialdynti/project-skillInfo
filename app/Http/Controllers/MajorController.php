<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MajorController extends Controller
{
    public function showmajor()
    {
        $data['profile'] = Auth::user();
        $data['major'] = Major::orderby('major_name', 'asc')->get();
        $data['major'] = Major::paginate(10);
        return view('admin.table-major', $data);
    }

    public function search(Request $request)
    {
        $data['profile'] = Auth::user();
        $search = $request->input('search');
        $query = Major::query();

        if ($search) {
            $query->where('major_name', 'LIKE', '%'.$request->search.'%')
            ->orwhere('description', 'LIKE', '%'.$request->search.'%')
            ->get();
        }

        $data['major'] = $query->paginate(10)->appends(['search' => $search]);
        return view('admin.table-major', $data);
    }

    public function create()
    {
        $data['profile'] = Auth::user();
        return view('admin.major-create', $data);
    }

    public function add(Request $request)
    {
        $request->validate([
            'major_name' => ['required', 'max:32'],
            'description' => 'required'
        ], [
            'major_name.required' => 'Major name cannot be empty',
            'major_name.max' => 'Major name maximal 32 character',
            'description.required' => 'Description cannot be empty'
        ]);

        $major = Major::create([
            'major_name' => $request->major_name,
            'description' => $request->description
        ]);

        if ($major) {
            Session::flash('message', 'Data saved successfully');
        } else {
            Session::flash('message', 'Data failed to save');
        }

        return redirect('table-major');
    }

    public function edit(Request $request)
    {
        $data['profile'] = Auth::user();
        $data['major'] = Major::find($request->id);
        return view('admin.major-edit', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'major_name' => 'required',
            'description' => 'required'
        ], [
            'major_name.required' => 'Major name cannot be empty',
            'description.required' => 'Description cannot be empty'
        ]);

        $update = Major::where('id', $request->id)->update([
            'major_name' => $request->major_name,
            'description' => $request->description
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-major');
    }

    public function delete(Request $request)
    {
        $major = Major::find($request->id);
        $delete = Major::where('id', $request->id)->delete();

        return redirect('table-major');
    }

}
