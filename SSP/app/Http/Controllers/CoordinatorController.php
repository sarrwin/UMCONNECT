<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supervisor;
use App\Models\Appointment;
use App\Models\Student;
use App\Models\Project;
use App\Models\ProjectStudent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderEmail;
class CoordinatorController extends Controller
{
    public function dashboard()
    {
      $user = Auth::user();
      $supervisor=$user->supervisor;
      $department = $supervisor->department;

        // Total number of students in the coordinator's department
        $totalStudents = Student::where('department', $department)->count();

        // Total number of supervisors in the coordinator's department
        $totalSupervisors = Supervisor::where('department', $department)->count();

        // Total number of projects in the coordinator's department
       $totalProjects = Project::where('department', $department)->count();

        //Total number of students with projects in the coordinator's department
        $studentsWithProjects = Project::where('department', $department)
            ->with('students')
            ->get()
            ->pluck('students')
            ->flatten()
            ->unique('id')
            ->count();

        // Total number of supervisors with projects in the coordinator's department
        $supervisorsWithProjects = Project::where('department', $department)
            ->with('supervisor')
            ->get()
            ->pluck('supervisor')
            ->unique('id')
            ->count();

            return view('coordinator.dashboard', compact('totalStudents', 'totalSupervisors', 'totalProjects', 'studentsWithProjects', 'supervisorsWithProjects'));
    }
    public function showAssignedProjects()
    {
        // Authenticated user
        $user = Auth::user();
        $supervisor = $user->supervisor;
    
        if (!$supervisor) {
            return redirect()->back()->withErrors('Supervisor data not found.');
        }
    
        // Department of the supervisor
        $department = $supervisor->department;
    
        // Fetch projects with related data in the department
        $projects = Project::where('department', $department)
            ->with(['supervisor', 'students'])
            ->get();
    
        // Fetch appointments related to the projects
        $appointments = Appointment::with(['supervisor', 'student'])
            ->whereIn('project_id', $projects->pluck('id'))
            ->get();
    
        // Fetch supervisors without projects in the department
        $supervisorsWithoutProjects = User::whereHas('supervisor', function ($query) use ($department) {
            $query->where('department', $department);
        })->whereDoesntHave('projects')->get();
    
      // Fetch students without projects in the department
      $studentsWithoutProjects = User::whereHas('student', function ($query) use ($department) {
        $query->where('department', $department);
    })->whereDoesntHave('assignedProjects')->get();
    
    

        // Return data to the view
        return view('coordinator.assigned_projects', compact(
            'projects',
            'appointments',
            'supervisorsWithoutProjects',
            'studentsWithoutProjects'
        ));
    }
    
    
public function projects(){
    $user = Auth::user();
    $supervisor = $user->supervisor;
    $department = $supervisor->department;

    // Fetch projects within the department
    $projects = Project::where('department', $department)
                        ->with('supervisor')
                        ->get();

 return view('coordinator.total_project', compact('projects'));
}

public function viewDepartmentLogbook($projectId)
{
    $user = Auth::user();

    // Ensure the user is a supervisor with coordinator access
    $supervisor = $user->supervisor;

    // Debugging: Check if the supervisor and coordinator status are retrieved correctly
    //dd('Supervisor:', $supervisor, 'Is Coordinator:', $supervisor->is_coordinator);

    if (!$supervisor || !$supervisor->is_coordinator) {
        abort(403, 'Unauthorized access');
    }

    // Retrieve the project if it matches the coordinator's department
    $project = Project::where('id', $projectId)
                      ->where('department', $supervisor->department)
                      ->with(['logbooks.entries.logbookFiles', 'supervisor'])
                      ->first();

    // Debugging: Check if the project data is retrieved and matches the department
    //dd('Department:', $supervisor->department, 'Project Department:', $project->department, 'Project:', $project);

    if (!$project) {
        abort(403, 'Project not found or unauthorized access');
    }

    return view('coordinator.logbook', compact('project'));
}

public function studentsList()
{
    $user = Auth::user();
    $supervisor = $user->supervisor;
    $department = $supervisor->department;

    // Fetch students in the coordinator's department
    $students = Student::where('department', $department)->get();

    return view('coordinator.student_list', compact('students'));
}

public function supervisorsList()
{
    $user = Auth::user();
    $supervisor = $user->supervisor;
    $department = $supervisor->department;

    // Fetch students in the coordinator's department
    $supervisor = Supervisor::where('department', $department)->get();

    return view('coordinator.supervisor_list', compact('supervisor'));
}


public function remind($projectId)
{
    $project = Project::with(['supervisor', 'students'])->findOrFail($projectId);

    $recipients = collect();

    // Add supervisor email
    if ($project->supervisor) {
        $recipients->push($project->supervisor->email);
    }

    // Add student emails
    $recipients = $recipients->merge($project->students->pluck('email'));

    // Send email using ReminderEmail Mailable
    foreach ($recipients as $email) {
        Mail::to($email)->send(new ReminderEmail($project));
    }

    return redirect()->back()->with('success', 'Reminder sent to the supervisor and students.');
}
}