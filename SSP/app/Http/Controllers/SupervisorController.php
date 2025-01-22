<?php
// app/Http/Controllers/SupervisorController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supervisor;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve the department filter value from the request
        $selectedDepartment = $request->input('department', ''); // Default to empty if not provided
    
        // Query to fetch supervisors, optionally filtered by department
        $query = User::where('role', 'supervisor');
    
        if (!empty($selectedDepartment)) {
            $query->whereHas('supervisor', function ($q) use ($selectedDepartment) {
                $q->where('department', $selectedDepartment);
            });
        }
    
        $supervisors = $query->get();
    
        // Get the list of all distinct departments
        $departments = Supervisor::distinct()->pluck('department');
    
        // Pass data to the view
        return view('/students/supervisors.index', compact('supervisors', 'departments', 'selectedDepartment'));
    }
    

    public function show(User $supervisor)
    {
        if ($supervisor->role !== 'supervisor') {
            abort(404);
        }

        return view('/students/supervisors.show', compact('supervisor'));
    }
}
