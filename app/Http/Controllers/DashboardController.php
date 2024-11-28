<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\Competency_element;
use App\Models\Competency_standard;
use App\Models\Examination;
use App\Models\Major;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    // DASHBOARD ADMIN
    public function show(){
        $user = Auth::user();
        $data = [
            'total_user' => User::count(),
            'total_student' => Student::count(),
            'total_major' => Major::count(),
            'total_ass' => Assessor::count(),
            'total_cs' => Competency_standard::count(),
            'total_exam' => Examination::count()
        ];
        return view('admin.dashboard', ['user' => $user] + $data);
    }

    public function profile(Request $request)
    {
        $data['user'] = User::find($request->id);
        return view('admin.detail-profile', $data);
    }

    public function updtprofile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'full_name' => 'required',
            'username' => ['required', 'min:6', 'max:12'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone_number' => ['required', 'numeric'],
            'image' => 'image'
        ], [
            'full_name.required' => 'Full name cannot be empty',
            'username.required' => 'Username cannot be empty',
            'username.min' => 'Minimum username must be 6 characters',
            'username.max' => 'Maximum username 6 characters',
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Enter a valid email',
            'password' => 'Password cannot be empty',
            'password.min' => 'Minimum password must be 6 characters',
            'phone_number.required' => 'Phone number cannot be empty',
            'phone_number.numeric' => 'Input in the form of numbers',
            'image.image' => 'Photos must be in the correct format'
        ]);

        $fileName = $request->old_image;
        if ($request->hasFile('image')) {
            $extfile = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . "." .$extfile;
            $request->file('image')->storeAs('public/image', $fileName);

            if ($request->old_image) {
                Storage::delete('public/image/'. $request->old_image);
            }
        }

        $update = User::where('id', $request->id)->update([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : DB ::raw('password'),
            'phone_number' => $request->phone_number,
            'image' => $fileName
        ]);

        if ($update) {
            Session::flash('message', 'Data profile changed successfully');
            Session::flash('alert-class', 'alert-success');
        } else {
            Session::flash('message', 'Data profile failed to change');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('/dashboard');
    }


    // DASHBOARD ASSESSOR
    public function showass()
    {
        $user = Auth::user();
        $assessorId = $user->id;

        $data = [
            // 'total_cs' => Competency_standard::count(),
            // 'total_ce' => Competency_element::count()
          'total_cs' => Competency_standard::where('assessors_id', $assessorId)->count(),
        // 'total_ce' => Competency_element::where('assessors_id', $assessorId)->count(),
        ];

        return view('assessor.dashboard', ['user' => $user] + $data);
    }

    public function profileass(Request $request)
    {
        $data['user'] = User::find($request->id);
        return view('assessor.detail-profile', $data);
    }

    public function updtprofileass(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'full_name' => 'required',
            'username' => ['required', 'min:6', 'max:12'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone_number' => ['required', 'numeric'],
            'image' => 'image'
        ], [
            'full_name.required' => 'Full name cannot be empty',
            'username.required' => 'Username cannot be empty',
            'username.min' => 'Minimum username must be 6 characters',
            'username.max' => 'Maximum username 6 characters',
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Enter a valid email',
            'password' => 'Password cannot be empty',
            'password.min' => 'Minimum password must be 6 characters',
            'phone_number.required' => 'Phone number cannot be empty',
            'phone_number.numeric' => 'Input in the form of numbers',
            'image.image' => 'Photos must be in the correct format'
        ]);

        $fileName = $request->old_image;
        if ($request->hasFile('image')) {
            $extfile = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . "." .$extfile;
            $request->file('image')->storeAs('public/image', $fileName);

            if ($request->old_image) {
                Storage::delete('public/image/'. $request->old_image);
            }
        }

        $update = User::where('id', $request->id)->update([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : DB ::raw('password'),
            'phone_number' => $request->phone_number,
            'image' => $fileName
        ]);

        if ($update) {
            Session::flash('message', 'Data profile changed successfully');
            Session::flash('alert-class', 'alert-success');
        } else {
            Session::flash('message', 'Data profile failed to change');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('/table-competency_standard');
    }

    // DASHBOARD STUDENT
    public function showstud()
    {
        $user = Auth::user();
        $userId = Auth::id();
        $student = Student::where('users_id', $userId)->first();
        $exam = Examination::where('students_id', $student->id)->count();
        return view('student.dashboard-stud', compact('user', 'exam'));
    }

    public function profilestud(Request $request)
    {
        $data['user'] = User::find($request->id);
        return view('student.detail-profile', $data);
    }

    public function updtprofilestud(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'full_name' => 'required',
            'username' => ['required', 'min:6', 'max:12'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone_number' => ['required', 'numeric'],
            'image' => 'image'
        ], [
            'full_name.required' => 'Full name cannot be empty',
            'username.required' => 'Username cannot be empty',
            'username.min' => 'Minimum username must be 6 characters',
            'username.max' => 'Maximum username 6 characters',
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Enter a valid email',
            'password' => 'Password cannot be empty',
            'password.min' => 'Minimum password must be 6 characters',
            'phone_number.required' => 'Phone number cannot be empty',
            'phone_number.numeric' => 'Input in the form of numbers',
            'image.image' => 'Photos must be in the correct format'
        ]);

        $fileName = $request->old_image ?? null;
        if ($request->hasFile('image')) {
            $extfile = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . "." .$extfile;
            $request->file('image')->storeAs('public/image', $fileName);

            if ($request->old_image) {
                Storage::delete('public/image/'. $request->old_image);
            }
        }

        $update = User::where('id', $request->id)->update([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,    
            'phone_number' => $request->phone_number,
            'image' => $fileName
        ]);

        if ($update) {
            Auth::loginUsingId(Auth::id());
            Session::flash('message', 'Data profile changed successfully');
            Session::flash('alert-class', 'alert-success');
        } else {
            Session::flash('message', 'Data profile failed to change');
            Session::flash('alert-class', 'alert-danger');
        }

        return redirect('/dashboardStudent');
    }
}
