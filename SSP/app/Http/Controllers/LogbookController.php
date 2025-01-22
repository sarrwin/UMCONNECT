<?php
namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\LogbookEntry;
use App\Models\LogbookFile;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

class LogbookController extends Controller
{
    public function create()
    {
        $projects = Auth::user()->assignedProjects()->get();
        return view('students.logbook.create', compact('projects'));
    }
    // app/Http/Controllers/LogbookController.php



    public function generatePDF(Project $project)
    {
        // Fetch the logbook for the project
        $logbook = Logbook::where('project_id', $project->id)
                          ->with(['entries.student'])
                          ->firstOrFail();
    
        // Fetch all entries related to the logbook
        $filteredEntries = $logbook->entries()->with(['student'])->get();
    
        // Render the Blade view into HTML
        $html = view('students.logbook.pdf', compact('logbook', 'project', 'filteredEntries'))->render();
    
        // Configure DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
    
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Return the PDF as a download
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="logbook-' . $project->id . '.pdf"');
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'activity' => 'required|string',
            'activity_date' => 'required|date',
            'reference_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            'report_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);
    
        $logbook = Logbook::firstOrCreate(['project_id' => $request->project_id]);
    
        $logbookEntry = LogbookEntry::create([
            'logbook_id' => $logbook->id,
            'student_id' => Auth::id(),
            'activity' => $request->activity,
            'activity_date' => $request->activity_date,
            'verified' => false,
        ]);
    
        if ($request->hasFile('reference_file')) {
            $referenceFilePath = $request->file('reference_file')->store('logbook_files','public');
            LogbookFile::create([
                'logbook_entry_id' => $logbookEntry->id,
                'file_path' => $referenceFilePath,
                'file_type' => $request->file('reference_file')->getClientOriginalExtension(),
            ]);
        }
    
        if ($request->hasFile('report_file')) {
            $reportFilePath = $request->file('report_file')->store('logbook_files');
            LogbookFile::create([
                'logbook_entry_id' => $logbookEntry->id,
                'file_path' => $reportFilePath,
                'file_type' => $request->file('report_file')->getClientOriginalExtension(),
            ]);
        }
    
        return redirect()->route('students.logbook.index')->with('success', 'Logbook entry created successfully.');
    }

    public function edit(LogbookEntry $logbookEntry)
    {
        if ($logbookEntry->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('students.logbook.edit', compact('logbookEntry'));
    }

    public function update(Request $request, LogbookEntry $logbookEntry)
    {
        // Ensure the user is authorized to update the logbook entry
        if ($logbookEntry->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    
        // Validate the input
        $request->validate([
            'activity' => 'required|string',
            'activity_date' => 'required|date',
            'reference_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);
    
        // Update the activity and activity date
        $logbookEntry->update([
            'activity' => $request->activity,
            'activity_date' => $request->activity_date,
        ]);
    
        // Handle file removal if requested
        if ($request->has('remove_file') && $request->remove_file == 1) {
            $file = $logbookEntry->logbookFiles()->first(); // Get the first associated file
            if ($file) {
                // Delete the file from storage
                Storage::disk('public')->delete($file->file_path);
                // Remove the file record from the database
                $file->delete();
            }
        }
    
        // Handle file upload if a new file is provided
        if ($request->hasFile('reference_file')) {
            $referenceFilePath = $request->file('reference_file')->store('logbook_files', 'public');
            $logbookEntry->logbookFiles()->updateOrCreate(
                ['logbook_entry_id' => $logbookEntry->id], // Match on logbook entry ID
                [
                    'file_path' => $referenceFilePath,
                    'file_type' => $request->file('reference_file')->getClientOriginalExtension(),
                ]
            );
        }
    
        // Redirect back with a success message
        return redirect()->route('students.logbook.index')->with('success', 'Logbook entry updated successfully.');
    }
    

    public function destroy(LogbookEntry $logbookEntry)
    {
        if ($logbookEntry->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $logbookEntry->delete();

        return redirect()->route('students.logbook.index')->with('success', 'Logbook entry deleted successfully.');
    }

    public function showFile(LogbookFile $logbookFile)
{
    $user = Auth::user();
    $logbookEntry = $logbookFile->logbookEntry;
    $project = $logbookEntry->logbook->project;

    // // Check if the user is the owner of the logbook entry or the supervisor of the project
    // if ($user->id !== $logbookEntry->student_id && $user->id !== $project->supervisor_id) {
    //     abort(403, 'Unauthorized action.');
    // }

    return response()->file(storage_path('app/' . $logbookFile->file_path));
}
    private function isSupervisorOfProject($user, $project)
    {
        return $user->role === 'supervisor' && $user->id === $project->supervisor_id;
    }

    public function index(Request $request, Project $project)
{
    // Fetch the logbook for the project
    $logbook = Logbook::where('project_id', $project->id)
                      ->with(['entries.student', 'entries.logbookFiles'])
                      ->firstOrFail();

    // Apply filters
    $query = $logbook->entries()->with(['student', 'logbookFiles']);

    if ($request->filled('student_id')) {
        $query->where('student_id', $request->student_id);
    }

    if ($request->filled('verified')) {
        $query->where('verified', $request->verified);
    }

    $filteredEntries = $query->get();

    // Fetch unique students for the dropdown
    $students = $logbook->entries->pluck('student')->unique('id');

    return view('supervisor.logbook.index', compact('logbook', 'project', 'filteredEntries', 'students', 'request'));
}

    public function indexStudent(Request $request)
    {
        $projects = Auth::user()->assignedProjects()->get();
    
        // Get the project assigned to the authenticated student
        $project = Project::whereHas('students', function ($query) {
            $query->where('student_id', Auth::id());
        })->first();
    
        if (!$project) {
            return view('students.projects.no_project'); // View for users with no assigned project
        }
        // Initialize query for logbook entries related to the project
        $query = LogbookEntry::whereHas('logbook.project', function ($query) use ($project) {
            $query->where('project_id', $project->id);
        })->with(['logbook.project', 'logbookFiles']);
    
        // Apply filters if present
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('logbook.project', function ($q2) use ($request) {
                    $q2->where('title', 'like', '%' . $request->search . '%');
                })->orWhere('activity', 'like', '%' . $request->search . '%');
            });
        }
    
        if ($request->filled('project_id')) {
            $query->whereHas('logbook.project', function ($q) use ($request) {
                $q->where('id', $request->project_id);
            });
        }
    
        if ($request->filled('verified')) {
            $query->where('verified', $request->verified);
        }
    
        // Fetch the filtered logbook entries
        $logbookEntries = $query->orderBy('activity_date', 'asc')->get();
    
        // Return the view with filters for better user experience
        $filters = [
            'search' => $request->search,
            'project_id' => $request->project_id,
            'verified' => $request->verified,
        ];
    
        return view('students.logbook.index', compact('logbookEntries', 'projects', 'filters'));
    }
    

    public function viewStudentLogbooks(User $student)
    {
        $logbookEntries = LogbookEntry::where('student_id', $student->id)
                                      ->with(['logbook.project', 'logbookFiles'])
                                      ->get();
        return view('supervisor.logbook.view_student', compact('logbookEntries', 'student'));
    }

    public function verify(LogbookEntry $logbookEntry)
    {
        if ($logbookEntry->logbook->project->supervisor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $logbookEntry->update(['verified' => true]);

        return redirect()->route('supervisor.logbook.index', $logbookEntry->logbook->project_id)->with('success', 'Logbook entry verified successfully.');
    }

    public function unverify(LogbookEntry $logbookEntry)
{
    if ($logbookEntry->logbook->project->supervisor_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $logbookEntry->update(['verified' => false]);

    return redirect()->route('supervisor.logbook.index', $logbookEntry->logbook->project_id)->with('success', 'Logbook entry unverified successfully.');
}

    public function uploadReferenceDocument(Request $request, LogbookEntry $logbookEntry)
    {
        if ($logbookEntry->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'
        ]);

        $filePath = $request->file('file')->store('logbook_files');

        LogbookFile::create([
            'logbook_entry_id' => $logbookEntry->id,
            'file_path' => $filePath,
            'file_type' => $request->file->getClientOriginalExtension(),
        ]);

        return redirect()->route('students.logbook.index')->with('success', 'Reference document uploaded successfully.');
    }

    public function uploadReportDocument(Request $request, LogbookEntry $logbookEntry)
    {
        if ($logbookEntry->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'
        ]);

        $filePath = $request->file('file')->store('logbook_files');

        LogbookFile::create([
            'logbook_entry_id' => $logbookEntry->id,
            'file_path' => $filePath,
            'file_type' => $request->file->getClientOriginalExtension(),
        ]);

        return redirect()->route('students.logbook.index')->with('success', 'Report document uploaded successfully.');
    }

    public function downloadFile(LogbookFile $logbookFile)
    {
        $user = Auth::user();
        $logbookEntry = $logbookFile->logbookEntry;

        // Check if the user is the owner of the logbook entry or a supervisor of the project
        if ($user->id !== $logbookEntry->student_id && !$this->isSupervisorOfProject($user, $logbookEntry->logbook->project)) {
            abort(403, 'Unauthorized action.');
        }

        return response()->download(storage_path('app/' . $logbookFile->file_path));
    }
}
