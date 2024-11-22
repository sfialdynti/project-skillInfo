<?php

namespace App\Http\Controllers;

use App\Models\Assessor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AssessorController extends Controller
{
    public function internal()
    {
        $data['profile'] = Auth::user();
        $data['assessors'] = Assessor::with('users')->where('assessor_type', 'Internal')->get();

        return view('admin.table-assessorint', $data);
    }

    public function searchInternal(Request $request)
    {
        $profile = Auth::user();
        $search = $request->input('search');
        $assessors = Assessor::query()->with('users')
        ->where('assessor_type', 'Internal') 
        ->when($search, function ($q) use ($search) {
            $q->whereHas('users', function ($user) use ($search) {
                $user->where('full_name', 'LIKE', '%' . $search . '%');
            });
        })->paginate(10);

        return view('admin.table-assessorint', compact('assessors', 'profile'));
    }

    public function createInternal()
    {
        $profile = Auth::user();
        $user = User::where('role', 'Assessor')
        ->leftJoin('assessors', 'users.id', '=', 'assessors.users_id')
        ->select('users.*', 'assessors.assessor_type')
        ->get();

        return view('admin.assessorint-create', compact('user', 'profile'));
    }

    public function editInternal(Request $request)
    {
        $profile = Auth::user();
        $assessor = Assessor::with('users')->where('assessor_type', 'Internal')->findOrFail($request->id);
        $user = User::where('role', 'Assessor')->get();

        return view('admin.assessorint-edit', compact('assessor', 'user', 'profile'));
    }

    public function updateInternal(Request $request)
    {
        $request->validate([
            'users_id' => ['required', 'exists:users,id'],
            'description' => 'required'
        ], [
            'users_id.required' => 'User cannot be empty',
            'description.required' => 'Description cannot be empty',
        ]);

        $assessor = Assessor::findOrFail($request->id);
        if ($assessor->assessor_type != 'Internal') {
            return redirect()->route('assessors.index')->with('error', 'Only Internal assessors can be updated.');
        }

        $update = Assessor::where('id', $request->id)->update([
            'users_id' => $request->users_id,
            'description' => $request->description,
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-assessorint');
    }

    public function deleteint(Request $request)
    {
        $assessors = Assessor::find($request->id);
        $delete = Assessor::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        } else {
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('table-assessorint');
    }

    public function external()
    {
        $data['profile'] = Auth::user();
        $data['assessors'] = Assessor::with('users')->where('assessor_type', 'External')->get();
        return view('admin.table-assessorext', $data);
    }

    public function searchExternal(Request $request)
    {
        $profile = Auth::user();
        $search = $request->input('search');
        $assessors = Assessor::query()->with('users')
        ->where('assessor_type', 'External') 
        ->when($search, function ($q) use ($search) {
            $q->whereHas('users', function ($user) use ($search) {
                $user->where('full_name', 'LIKE', '%' . $search . '%');
            });
        })
        ->paginate(10);

        return view('admin.table-assessorext', compact('assessors', 'profile'));
    }

    public function createExternal()
    {
        $profile = Auth::user();
        $user = User::where('role', 'Assessor')
        ->leftJoin('assessors', 'users.id', '=', 'assessors.users_id')
        ->select('users.*', 'assessors.assessor_type')
        ->get();
            
        return view('admin.assessorext-create', compact('user', 'profile'));
    }

    public function editExternal(Request $request)
    {
        $profile = Auth::user();
        $assessor = Assessor::with('users')->where('assessor_type', 'External')->findOrFail($request->id);
        $user = User::where('role', 'Assessor')->get();

        return view('admin.assessorext-edit', compact('assessor', 'user', 'profile'));
    }

    public function updateExternal(Request $request)
    {
        $request->validate([
            'users_id' => ['required', 'exists:users,id'],
            'description' => 'required'
        ], [
            'users_id.required' => 'User cannot be empty',
            'description.required' => 'Description cannot be empty',
        ]);

        $assessor = Assessor::findOrFail($request->id);
        
        $update = Assessor::where('id', $request->id)->update([
            'users_id' => $request->users_id,
            'description' => $request->description,
        ]);

        if ($update) {
            Session::flash('message', 'Data changed successfully');
        } else {
            Session::flash('message', 'Data failed to change');
        }

        return redirect('table-assessorext');
    }

    public function deleteext(Request $request)
    {
        $assessors = Assessor::find($request->id);
        $delete = Assessor::where('id', $request->id)->delete();
        if ($delete) {
            Session::flash('message', 'Data deleted successfully');
        } else {
            Session::flash('message', 'Data failed to delete');
        }

        return redirect('table-assessorext');
    }

    public function add(Request $request, $type)
    {
        $request->validate([
            'users_id' => ['required', 'exists:users,id'],
            'assessor_type' => ['required', 'in:Internal,External'],
            'description' => 'required'
        ], [
            'users_id.required' => 'User cannot be empty',
            'assessor_type.required' => 'Assessor type cannot be empty',
            'assessor_type.in' => 'Assessor type must be either Internal or External',
            'description.required' => 'Description cannot be empty',
        ]);

        $assessors = Assessor::create([
            'users_id' => $request->users_id,
            'assessor_type' => $type,
            'description' => $request->description
        ]);

        if ($assessors) {
            Session::flash('message', 'Data saved successfully');
        } else {
            Session::flash('message', 'Data failed to save');
        }

        if ($type === 'Internal') {
            return redirect('table-assessorint');
        } elseif ($type === 'External') {
            return redirect('table-assessorext');
        } else {
            return redirect()->back()->with('error', 'Invalid assessor type');
        }
        
        // return redirect('table-assessorint');

    }
}
