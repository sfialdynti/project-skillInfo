<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getEnumValues($table, $column)
    {
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'")[0]->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $enum = array_map(function ($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));

        return $enum;
    }

    public function showuser()
    {
        $data['profile'] = Auth::user();
        $data['user'] = User::orderby('full_name', 'asc')->get();
        $data['user'] = User::paginate(10);

        return view('admin.table-user', $data);
    }

    public function search(Request $request)
    {
        $data['profile'] = Auth::user();
        $search = $request->input('search');
        $query = User::query();

        if ($search) {
            $query->where('full_name', 'LIKE', '%'.$request->search.'%')
            ->orWhere('email', 'LIKE', '%'.$request->search.'%')
            ->get();
        }

        $data['user'] = $query->paginate(10)->appends(['search' => $search]);

        return view('admin.table-user', $data);
    }

    public function create()
    {
        $profile = Auth::user();
        $roles = $this->getEnumValues('users', 'role');

        return view('admin.user-create', compact('roles', 'profile'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'username' => ['required', 'min:6', 'max:12', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'phone_number' => ['required', 'numeric'],
            'role' => 'required|in:'. implode(',', $this->getEnumValues('users', 'role')),
            'image' => 'image'
        ], [
            'full_name.required' => 'Full name cannot be empty',
            'username.required' => 'Username cannot be empty',
            'username.min' => 'Minimum username must be 6 characters',
            'username.max' => 'Maximum username 12 characters',
            'username.unique' => 'Username is already in use',
            'email.required' => 'Email cannot be empty',
            'email.email' => 'Enter a valid email',
            'email.unique' => 'Email is already in use',
            'password' => 'Password cannot be empty',
            'password.min' => 'Minimum password must be 6 characters',
            'phone_number.required' => 'Phone number cannot be empty',
            'phone_number.numeric' => 'Input in the form of numbers',
            'role.required' => 'Select role',
            'role.in' => 'Invalid role selected',
            'image.image' => 'Photos must be in the correct format'
        ]);

        $fileName = '';
        if ($request->file('image')) {
            $extFile = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . "." . $extFile;
            $request->file('image')->storeAs('public/image', $fileName);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : DB ::raw('password'),
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'image' => $fileName
        ]);

        if ($user) {
            Session::flash('message', 'Data saved successfully');
        } else {
            Session::flash('message', 'Data failed to save');
        }

        return redirect('table-user');
    }

    public function edit(Request $request)
    {
        $profile = Auth::user();
        $user = User::find($request->id);
        $enumValues = $this->getEnumValues('users', 'role');

        return view('admin.user-edit', compact('user', 'enumValues', 'profile'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id); 

        $request->validate([
            'full_name' => 'required',
            'username' => ['required', 'min:6', 'max:12'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone_number' => ['required', 'numeric'],
            'role' => 'required|in:'. implode(',', $this->getEnumValues('users', 'role')),
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
            'role.required' => 'Select role',
            'role.in' => 'Invalid role selected',
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

        $password = $user->password;
        if ($request->filled('password')) {
            $password = bcrypt($request->password);
        }

        $update = User::where('id', $request->id)->update([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : DB ::raw('password'),
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'image' => $fileName
        ]);

        if ($request->hasFile('image') || $request->old_image == null) {
            $updateData['image'] = $fileName;
        }

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-user');
    }

    public function delete(Request $request)
    {
        $user = User::find($request->id);
        $delete = User::where('id', $request->id)->delete();
        if ($delete) {
            if ($user->image) {
                Storage::delete('image/' .$user->image);
            }
            Session::flash('message', 'Data deleted successfully');
        } else {
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('table-user');
    }
}
