<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\Coordinator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
class AdminController extends Controller
{

    public function index(Request $request)
{
    $role = $request->input('role');
    $department = $request->input('department');

    // Initialize the query
    $query = User::query();

    // Apply role-based filtering
    if ($role === 'student') {
        $query->where('role', 'student');
    } elseif ($role === 'supervisor') {
        $query->where('role', 'supervisor')->whereHas('supervisor', function ($q) {
            $q->where('is_coordinator', 0);
        });
    } elseif ($role === 'coordinator') {
        $query->where('role', 'supervisor')->whereHas('supervisor', function ($q) {
            $q->where('is_coordinator', 1);
        });
    }

    // Apply department-based filtering
    if ($department) {
        $query->whereHas('student', function ($q) use ($department) {
            $q->where('department', $department);
        })->orWhereHas('supervisor', function ($q) use ($department) {
            $q->where('department', $department);
        });
    }

    // Fetch the users based on the query
    $users = $query->with('supervisor', 'student')->get();

    // Fetch distinct departments from students and supervisors tables
    $studentDepartments = \App\Models\Student::distinct()->pluck('department')->toArray();
    $supervisorDepartments = \App\Models\Supervisor::distinct()->pluck('department')->toArray();

    // Combine and remove duplicate departments
    $departments = array_unique(array_merge($studentDepartments, $supervisorDepartments));

    // Pass users and departments to the view
    return view('admin.index', compact('users', 'departments'));
}


    public function verifyCoordinator(Supervisor $supervisor): RedirectResponse
{
    $supervisor->update(['is_coordinator' => true]);

    Coordinator::create([
        'user_id' => $supervisor->supervisor_id,
        'department' => $supervisor->department,
        'staff_id' => $supervisor->staff_id, // Include staff_id here
    ]);

    return redirect()->back()->with('success', 'Supervisor verified as coordinator.');
}

public function demoteCoordinator(Supervisor $supervisor): RedirectResponse
{
    $supervisor->update(['is_coordinator' => false]);

    // Delete the corresponding coordinator record
    Coordinator::where('user_id', $supervisor->supervisor_id)->delete();

    return redirect()->back()->with('success', 'Coordinator demoted to supervisor.');
}

public function deleteUser(User $user): RedirectResponse
{
    // Ensure the user being deleted is not the current admin
    if (auth()->id() === $user->id) {
        return redirect()->back()->with('error', 'You cannot delete your own account.');
    }

    // Delete related records if necessary
    if ($user->role === 'supervisor') {
        Supervisor::where('user_id', $user->id)->delete();
        Coordinator::where('user_id', $user->id)->delete();
    } elseif ($user->role === 'student') {
        $user->student()->delete();
    }

    // Delete the user
    $user->delete();

    return redirect()->back()->with('success', 'User deleted successfully.');
}
}

