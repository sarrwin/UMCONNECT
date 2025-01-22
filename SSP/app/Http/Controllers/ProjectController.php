<?php

namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectFile;
use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\FileApprovalNotification;
use Illuminate\Validation\Rule; // Import Rule
class ProjectController extends Controller
{
    public function indexSupervisorProjects()
    {
        $projects = Auth::user()->projects()->with('students', 'files')->get();
        return view('supervisor.students.projects.index', compact('projects'));
    }

    public function viewSupervisorProject(Project $project)
    {
        if ($project->supervisor_id === null || $project->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $project->load('students', 'files');
        return view('supervisor.students.projects.view', compact('project'));
    }

    public function viewStudentProject(User $student)
    {
        $project = $student->assignedProjects()->with('supervisor', 'students', 'files','tasks')->first();

        if ($project->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('supervisor.students.projects.view_student_project', compact('project'));
    }

    public function myProject()
{
    $project = Auth::user()->assignedProjects()
        ->with([
            'supervisor',
            'students',
            'files',
            'tasks'
        ])
        ->first();

    if (!$project) {
        return view('students.projects.no_project'); // View for users with no assigned project
    }

    $taskCategories = $project->tasks->groupBy('category'); // Group tasks by category

    // Prepare tasks for Gantt chart
    $timelineTasks = $project->tasks->map(function ($task) {
        return [
            'id' => $task->id,
            'text' => $task->title,
            'start_date' => $task->start_date,
            'end_date' => $task->due_date,
            'status' => $task->status,
        ];
    });

    return view('students.projects.my_project', compact('project', 'taskCategories', 'timelineTasks'));
}

public function submitFile(Request $request, Project $project)
{
    try {
        \Log::info('File submission started.', [
            'project_id' => $project->id,
            'student_id' => Auth::id(),
            'file_type' => $request->file_type,
        ]);

        $request->validate([
            'file_type' => 'required|string',
            'file' => 'required|file|max:2048|mimes:pdf,docx,jpeg,png', // Example validation
        ]);

        $fileType = $request->file_type === 'other' ? $request->other_file_type : $request->file_type;

        \Log::info('File type determined.', ['file_type' => $fileType]);

        $existingFile = ProjectFile::where('project_id', $project->id)
            ->where('student_id', Auth::id())
            ->where('file_type', $fileType)
            ->latest('version')
            ->first();

        $version = $existingFile ? $existingFile->version + 1 : 1;

        \Log::info('Version determined.', [
            'existing_file_id' => $existingFile->id ?? null,
            'previous_version' => $existingFile->version ?? null,
            'new_version' => $version,
        ]);

        $path = $request->file('file')->store('project_files', 'public');

        \Log::info('File stored successfully.', [
            'file_path' => $path,
        ]);

        ProjectFile::create([
            'project_id' => $project->id,
            'student_id' => Auth::id(),
            'file_type' => $fileType,
            'file_path' => $path,
            'version' => $version,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        \Log::info('File submission completed.', [
            'project_id' => $project->id,
            'student_id' => Auth::id(),
            'file_type' => $fileType,
            'version' => $version,
        ]);

        return redirect()->route('students.projects.my_project')->with('success', 'File submitted successfully.');
    } catch (\Exception $e) {
        \Log::error('File submission failed.', [
            'error_message' => $e->getMessage(),
            'project_id' => $project->id,
            'student_id' => Auth::id(),
        ]);

        return redirect()->back()->withErrors('An error occurred while submitting the file. Please try again.');
    }
}

    public function showLeaderboard()
{
    // Fetch all projects supervised by the authenticated supervisor
    $projects = Project::with(['tasks', 'students'])
        ->where('supervisor_id', Auth::id())
        ->get();

    // Prepare leaderboard data
    $leaderboard = $projects->map(function ($project) {
        // Calculate progress percentage
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return [
            'title' => $project->title,
            'students' => $project->students,
            'progress' => $progress,
        ];
    });

    // Sort projects by progress in descending order
    $sortedLeaderboard = $leaderboard->sortByDesc('progress')->values();
    \Log::info('Leaderboard Data:', $sortedLeaderboard->toArray());

    return view('supervisor.leaderboard', ['projects' => $sortedLeaderboard]);
}

    public function viewFile(ProjectFile $projectFile)
    {
        $filePath = storage_path('app/' . $projectFile->file_path);
    
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }
    
        return response()->download($filePath);
    }
    public function indexAllProjects()
    {
        $projects = Project::with('supervisor', 'students')->paginate(10); ;
        return view('students.projects.index_all', compact('projects'));
    }
    

    public function index()
    {
        $projects = Project::where('supervisor_id', Auth::id())->with('students')->get();
        return view('supervisor.projects.index', compact('projects'));
    }

    public function create()
{
    // Get the supervisor's department
    $supervisor = auth()->user()->supervisor; // Ensure this retrieves the supervisor
    $department = $supervisor->department;

    // Fetch students who:
    // 1. Are not assigned to any project (using the `assignedProjects` relationship in the User model)
    // 2. Belong to the same department as the supervisor (using the department field in the students table)
    // 3. Have the role of 'student'
    $students = User::where('role', 'student')
        ->whereHas('student', function ($query) use ($department) {
            $query->where('department', $department); // Check department in the students table
        })
        ->whereDoesntHave('assignedProjects') // Check if the student has no associated projects
        ->get();

    return view('supervisor.projects.create', compact('students'));
}

    

    public function addComment(Request $request, $fileId)
{
    $request->validate([
        'comment' => 'required|string',
        'approval_status' => 'required|in:pending,approved,rejected',
    ]);

    $file = ProjectFile::findOrFail($fileId);
    $file->comment = $request->input('comment');
    $file->approval_status = $request->input('approval_status'); // Update the approval status
    $file->save();

    // Send email notification to the student
    $student = $file->student;
    $details = [
      'student_name' => $student->name,
        'file_type' => $file->file_type,
        'approval_status' => $file->approval_status,
        'comment' => $file->comment,
    ];
    \Log::info('Email Details:', $details);

    Mail::to($student->email)->send(new FileApprovalNotification($details));

    return redirect()->back()->with('success', 'Comment and approval status updated successfully.');
}

    public function deleteFile($fileId)
    {
        $file = ProjectFile::findOrFail($fileId);
        Storage::delete($file->file_path);
        $file->delete();

        return redirect()->route('students.projects.my_project')->with('success', 'File deleted successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'students_required' => 'required|integer|min:1',
            'students' => 'array', // Ensure students field is an array
            'students.*' => 'exists:users,id', // Ensure each student exists
            'session' => 'required|string|max:255',
        'department' => 'required|string|in:Artificial Intelligence,Software Engineering,Computer System and Network,Multimedia,Information System',
        'tools' => 'required|string|max:255',
        ]);

        $project = Project::create([
            'supervisor_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'students_required' => $request->students_required,
            'session' => $request->session,
             'department' => $request->department,
            'tools' => $request->tools,
        ]);

        

        if ($request->students) {
            $assignedStudents = $this->assignStudentsToProject($project, $request->students);

            if (!empty($assignedStudents['alreadyAssigned'])) {
                return redirect()->route('supervisor.projects.create')->with('warning', 'Some students are already assigned to another project: ' . implode(', ', $assignedStudents['alreadyAssigned']));
            }
        }

        return redirect()->route('supervisor.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        if (Auth::id() !== $project->supervisor_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('supervisor.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        if (Auth::id() !== $project->supervisor_id) {
            abort(403, 'Unauthorized action.');
        }

        $students = User::where('role', 'student')->get();
        return view('supervisor.projects.edit', compact('project', 'students'));
    }

    public function update(Request $request, Project $project)
    {
        if (Auth::id() !== $project->supervisor_id) {
            abort(403, 'Unauthorized action.');
        }
    
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'students_required' => 'required|integer|min:1',
            'students' => 'nullable|array|max:' . $request->students_required,
            'students.*' => 'exists:users,id',
            'session' => 'required|string|max:255',
            'department' => 'required|string|in:Artificial Intelligence,Software Engineering,Computer System and Network,Multimedia,Information System',
            'tools' => 'required|string|max:255',
        ]);
    
        // Update project details
        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'students_required' => $request->students_required,
            'session' => $request->session,
            'department' => $request->department,
            'tools' => $request->tools,
        ]);
    
        // Handle students association
        $updatedStudentIds = $request->students ?? []; // Default to an empty array if no students are selected
    
        // Sync students: add selected and remove unselected students
        $project->students()->sync($updatedStudentIds);
    
        // Check if there are any issues with already assigned students
        $assignedStudents = $this->assignStudentsToProject($project, $updatedStudentIds);
        if (!empty($assignedStudents['alreadyAssigned'])) {
            return redirect()
                ->route('supervisor.projects.edit', $project->id)
                ->with('warning', 'Some students are already assigned to another project: ' . implode(', ', $assignedStudents['alreadyAssigned']));
        }
    
        // Ensure the project room is updated
        $this->ensureProjectRoom($project);
    
        return redirect()->route('supervisor.projects.index')->with('success', 'Project updated successfully.');
    }
    
    public function destroy(Project $project)
    {
        if (Auth::id() !== $project->supervisor_id) {
            abort(403, 'Unauthorized action.');
        }

        $project->delete();
        return redirect()->route('supervisor.projects.index')->with('success', 'Project deleted successfully.');
    }

    public function indexStudent()
    {
        $projects = Auth::user()->assignedProjects()->with('supervisor')->get();
        return view('students.projects.index', compact('projects'));
    }

    private function assignStudentsToProject(Project $project, array $studentIds)
{
    $alreadyAssigned = [];
    $assigned = [];

    // Detach existing students
    $project->students()->detach();

    // Assign each student to the project
    foreach ($studentIds as $studentId) {
        $student = User::find($studentId);
        if ($student && !$student->assignedProjects()->exists()) {
            $project->students()->attach($studentId);
            $assigned[] = $student->name;
        } else {
            $alreadyAssigned[] = $student->name;
        }
    }

    // Ensure general announcement room exists
    $this->ensureAnnouncementRoom($project->supervisor_id);

    // Create project-specific chat room
    $this->createProjectRoom($project);

    return ['assigned' => $assigned, 'alreadyAssigned' => $alreadyAssigned];
}

    public function showProjects(Project $project)
    {
        return view('students.projects.details', compact('project'));
    }
    
    public function showSupProjects(Project $project)
    {
        return view('supervisor.projects.detail', compact('project'));
    }

    private function ensureAnnouncementRoom($supervisorId)
{
     // Ensure the supervisor has a general announcement room
     $chatRoom = ChatRoom::firstOrCreate([
        'supervisor_id' => $supervisorId,
        'is_announcement' => true,
    ], [
        'title' => 'General Announcement Room',
    ]);

    // Fetch students supervised by this supervisor (you may adjust this based on your relationships)
    $students = User::whereHas('assignedProjects', function ($query) use ($supervisorId) {
        $query->where('supervisor_id', $supervisorId);
    })->pluck('id')->toArray();

    // Link the students to the announcement room
    $chatRoom->students()->syncWithoutDetaching($students);
}

private function createProjectRoom(Project $project)
{
    // Find the existing project room by project ID
    $chatRoom = ChatRoom::where('project_id', $project->id)->first();

    if ($chatRoom) {
        // If a room exists, update its title with the new project title
        $chatRoom->update([
            'title' => 'Project Room: ' . $project->title,
        ]);
    } else {
        // If no room exists, create a new project room
        $chatRoom = ChatRoom::create([
            'supervisor_id' => $project->supervisor_id,
            'project_id' => $project->id,  // Ensure project_id is set
            'title' => 'Project Room: ' . $project->title,
            'is_announcement' => false,
        ]);
    }

    // Attach or sync students to the chat room
    $chatRoom->students()->sync($project->students->pluck('id')->toArray());
}



private function ensureProjectRoom($project)
{
    // Find the existing project room using project_id
    $chatRoom = ChatRoom::where('project_id', $project->id)->first();

    if ($chatRoom) {
        // If a room exists, update its title with the new project title
        $chatRoom->update([
            'title' => 'Project Room: ' . $project->title,
        ]);
    } else {
        // If no room exists, create a new one
        $chatRoom = ChatRoom::create([
            'supervisor_id' => $project->supervisor_id,
            'project_id' => $project->id,  // Set the project_id
            'title' => 'Project Room: ' . $project->title,
            'is_announcement' => false,
        ]);
    }

    // Fetch the students assigned to the project
    $students = $project->students->pluck('id')->toArray();

    // Link the students to the existing or newly created project chat room
    $chatRoom->students()->syncWithoutDetaching($students);
}


public function getTasks($projectId)
{
    try {
        \Log::info("Fetching tasks for project ID: $projectId");

        $project = Project::with('tasks')->findOrFail($projectId);
        
        \Log::info("Project found:", $project->toArray());

        $tasks = $project->tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'text' => $task->title,
                'start_date' => $task->start_date->format('Y-m-d'),
                'due_date' => $task->due_date->format('Y-m-d'),
                'duration' => $task->start_date->diffInDays($task->due_date) + 1,
                'status' => $task->status ,
                'assigned_to' => $task->student_id ? $task->student->name : null // Include assigned student name
            ];
        });

        \Log::info("Tasks mapped:", $tasks->toArray());

        return response()->json(['data' => $tasks]);

    } catch (\Exception $e) {
        \Log::error("Error fetching tasks: " . $e->getMessage());
        return response()->json(['error' => 'Could not load tasks'], 500);
    }
}


public function addTask(Request $request, $projectId, $userId)
{
    $messages = [
        'title.required' => 'The task title is required.',
        'start_date.required' => 'The start date is required.',
        'due_date.required' => 'The due date is required.',
        'assigned_to.required' => 'The assigned student is required for group projects.',
        'assigned_to.exists' => 'The selected student does not belong to this project.',
    ];

    try {
        // Load project with students
        $project = Project::with('students')->findOrFail($projectId);
        \Log::info('Loaded Students:', $project->students->toArray());

        // Check if the project is a single-student project
        $isSingleStudentProject = $project->students_required == 1;
        $assignedTo = $request->assigned_to;

        // For single-student projects, auto-assign the only student
        if ($isSingleStudentProject) {
            $assignedTo = $project->students->first()->id ?? null;
            if (!$assignedTo) {
                return response()->json(['error' => 'No student assigned to this project.'], 422);
            }
        } else {
            // For group projects, validate the assigned_to field
            $request->validate([
                'assigned_to' => [
                    'required',
                    'integer',
                    Rule::exists('project_student', 'student_id')->where(function ($query) use ($projectId) {
                        $query->where('project_id', $projectId);
                    }),
                ],
            ], $messages);

            $studentIds = $project->students->pluck('id')->toArray();
            if (!in_array($assignedTo, $studentIds)) {
                return response()->json(['error' => 'Invalid student for this project.'], 422);
            }
        }

        // Validate other fields
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:due_date',
            'due_date' => 'required|date|after_or_equal:start_date',
        ], $messages);

        // Create the task
        $task = $project->tasks()->create([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'status' => 'todo',
            'student_id' => $assignedTo,
        ]);

        \Log::info('Task Created:', $task->toArray());

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    } catch (ValidationException $e) {
        \Log::error('Validation Error:', $e->errors());
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        \Log::error('Unexpected Error:', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'An unexpected error occurred'], 500);
    }
}





public function updateTask(Request $request, $id)
{
    \Log::info("Update request received for task ID: $id", $request->all()); // Log all request data

    $task = Task::findOrFail($id);

    // Update fields based on request data
    if ($request->has('title')) {
        $task->title = $request->title;
        \Log::info("Updating title to: " . $task->title);
    }
    if ($request->has('start_date')) {
        $task->start_date = $request->start_date;
        \Log::info("Updating start_date to: " . $task->start_date);
    }
    if ($request->has('due_date')) {
        $task->due_date = $request->due_date;
        \Log::info("Updating due_date to: " . $task->due_date);
    }
    if ($request->has('status')) {
        $task->status = $request->status;
        \Log::info("Updating status to: " . $task->status);
    }
    if ($request->has('assigned_to')) {
        $task->student_id = $request->assigned_to;
        \Log::info("Updating assigned_to to: " . $task->student_id);
    }

    $task->save(); // Save changes to the database

    \Log::info("Task updated in database:", $task->toArray()); // Confirm updated task data in logs

    return response()->json(['success' => true, 'task' => $task]);
}
public function getTaskStats($projectId)
{
    $project = Project::with('students.tasks')->findOrFail($projectId);

    $stats = $project->students->map(function ($student) {
        $totalTasks = $student->tasks->count();
        $completedTasks = $student->tasks->where('status', 'completed')->count();
        $inProgressTasks = $student->tasks->where('status', 'in progress')->count();
        $todoTasks = $student->tasks->where('status', 'todo')->count();

        return [
            'name' => $student->name,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'todo_tasks' => $todoTasks,
        ];
    });

    return response()->json($stats);
}

public function delete($id)
{
    try {
        $task = Task::findOrFail($id); // Check if the task exists
        $task->delete(); // Delete the task

        return response()->json(['success' => true], 200); // Return JSON response
    } catch (\Exception $e) {
        // Log the error and return a 500 response
        \Log::error("Task deletion failed: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}










}
