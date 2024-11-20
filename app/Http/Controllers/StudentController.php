<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StudentController extends Controller
{
    public function showstud()
    {
        $data['profile'] = Auth::user();
        $data['student'] = Student::with(['users', 'majors'])->orderby('nisn', 'asc')->get();
        return view('table-student', $data);
    }

    public function search(Request $request)
    {
        $data['profile'] = Auth::user();
        $search = $request->input('search');
        $query = Student::query();

        if ($search) {
            $query->where('nisn', 'LIKE', '%'.$request->search.'%')
            ->orWhereHas('majors', function ($q) use ($search) {
                $q->where('major_name', 'LIKE', '%'.$search.'%');
            })
            ->orWhereHas('users', function ($qu) use ($search) {
                $qu->where('full_name', 'LIKE', '%'.$search.'%');
            });
        }

        $data['student'] = $query->paginate(10)->appends(['search' => $search]);
        return view('table-student', $data);
    }

    public function create()
    {
        $data['profile'] = Auth::user();
        $data['major'] = Major::all();
        $data['user'] = User::all();
        return view('student-create', $data);
    }

    public function add(Request $request)
    {
        $request->validate([
            'nisn' => ['required', 'max:10'],
            'grade_level' => 'required',
            'majors_id' => 'required',
            'users_id' => 'required',
        ], [
            'nisn.required' => 'NISN cannot be empty',
            'nisn.max' => 'Maximum 10 characters',
            'grade_level.required' => 'Grade level cannot be empty',
            'majors_id.required' => 'Major cannot be empty',
            'users_id.required' => 'User cannot be empty'
        ]);

        $student = Student::create([
            'nisn' => $request->nisn,
            'grade_level' => $request->grade_level,
            'majors_id' => $request->majors_id,
            'users_id' => $request->users_id
        ]);

        if ($student) {
            Session::flash('message', 'Data saved successfully');
        } else {
            Session::flash('message', 'Data failed to save');
        }

        return redirect('table-student');
    }

    public function edit(Request $request)
    {
        $data['profile'] = Auth::user();
        $data['student'] = Student::find($request->id);
        $data['major'] = Major::all();
        $data['user'] = User::all();
        return view('student-edit', $data);
    }

    public function update(Request $request)
    {
        $student = Student::findOrFail($request->id);
        $request->validate([
            'nisn' => ['required', 'max:10'],
            'grade_level' => 'required',
            'majors_id' => 'required',
            'users_id' => 'required',
        ], [
            'nisn.required' => 'NISN cannot be empty',
            'nisn.max' => 'Maximum 10 characters',
            'grade_level.required' => 'Grade level cannot be empty',
            'majors_id.required' => 'Major cannot be empty',
            'users_id.required' => 'User cannot be empty'
        ]);

        $update = Student::where('id', $request->id)->update([
            'nisn' => $request->nisn,
            'grade_level' => $request->grade_level,
            'majors_id' => $request->majors_id,
            'users_id' => $request->users_id
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-student');
    }

    public function delete(Request $request) 
    {
        $student = Student::find($request->id);
        $delete = Student::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        }else{
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('table-student');
    }
}
