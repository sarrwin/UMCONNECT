<?php
// app/Http/Controllers/SlotController.php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Models\Slot;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class SlotController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = Carbon::now()->toDateString();
    
        // Initialize the query for slots associated with the logged-in supervisor
        $query = Slot::where('supervisor_id', Auth::id());
    
        // Fetch the supervisor's projects for the dropdown filter
        $projects = Auth::user()->projects;
    
        // Apply filters based on the request
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            switch ($filter) {
                case 'upcoming':
                    $query->where('date', '>=', $currentDate);
                    break;
                case 'past':
                    $query->where('date', '<', $currentDate);
                    break;
                case 'available':
                    $query->where('booked', false); // Available slots
                    break;
                case 'booked':
                    $query->where('booked', true); // Booked slots
                    break;
            }
        } else {
            // Default behavior: only show slots from the current date onwards
            $query->where('date', '>=', $currentDate);
        }
    
        // Filter slots by project if 'project_id' is provided in the request
        if ($request->has('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }
    
        // Order slots by date
        $slots = $query->orderBy('date', 'asc')->paginate(5);
    
        // Set session flag if it doesn't exist
        if (!session()->has('google_auth_shown')) {
            session()->put('google_auth_shown', false);
            \Log::info('Session initialized: google_auth_shown set to false.');
        } else {
            \Log::info('Session value: google_auth_shown = ' . session('google_auth_shown'));
        }
    
        return view('slots.index', compact('slots', 'projects'));
    }
    

    public function create()
{
    $projects = Auth::user()->projects; // Get projects supervised by the current user
    return view('slots.create', compact('projects')); // Pass projects to view
}

public function store(Request $request)
{
    $request->validate([
        'date' => ['required', 'date', function ($attribute, $value, $fail) {
            if (Carbon::parse($value)->isBefore(Carbon::today())) {
                $fail('The selected date must not be in the past.');
            }
        }],
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'meeting_details' => 'nullable|string|max:255',
        'repeat_interval' => 'nullable|string|in:daily,weekly,biweekly,custom',
        'repeat_weeks' => 'nullable|integer|min:1|max:52',
        'project_id' => 'nullable|exists:projects,id',
    ]);

    $supervisorId = Auth::id();
    $date = Carbon::parse($request->date);
    $startTime = $request->start_time;
    $endTime = $request->end_time;
    $meetingDetails = $request->meeting_details;
    $repeatInterval = $request->repeat_interval;
    $repeatWeeks = (int) $request->repeat_weeks; // Ensure it is cast as an integer
    $projectId = $request->project_id;

    $currentDate = $date;

    while (!$request->repeat_end_date || $currentDate->lte(Carbon::parse($request->repeat_end_date))) {
        Slot::create([
            'supervisor_id' => $supervisorId,
            'project_id' => $projectId,
            'date' => $currentDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'meeting_details' => $meetingDetails,
        ]);

        if ($repeatInterval === 'daily') {
            $currentDate->addDay();
        } elseif ($repeatInterval === 'weekly') {
            $currentDate->addWeek();
        } elseif ($repeatInterval === 'biweekly') {
            $currentDate->addWeeks(2);
        } elseif ($repeatInterval === 'custom' && $repeatWeeks) {
            $currentDate->addWeeks($repeatWeeks);
        } else {
            break;
        }
    }

    return redirect()->route('slots.index')->with('success', 'Slot(s) created successfully.');
}





    

    public function destroy(Slot $slot)
    {
        if ($slot->supervisor_id != Auth::id()) {
            abort(403);
        }

        $slot->appointments()->delete();

        $slot->delete();
        return redirect()->route('slots.index')->with('success', 'Slot deleted successfully.');
    }

    public function show(User $supervisor)
    {
        $studentId = Auth::id();
    
        // Fetch the student's projects
        $projects = Project::whereHas('students', function ($query) use ($studentId) {
            $query->where('student_id', $studentId);
        })->get();
    
        // Check if the user is a supervisor
        if ($supervisor->role !== 'supervisor') {
            abort(404);
        }
    
        // Get request filters
        $projectId = request('project_id');
        $status = request('status');
    
        // Fetch filtered slots
        $slotsQuery = Slot::where('supervisor_id', $supervisor->id);
    
        // Apply project filter
        if (!empty($projectId)) {
            $slotsQuery->where('project_id', $projectId);
        }
    
        // Apply status filter
        if (!empty($status)) {
            if ($status == 'available') {
                $slotsQuery->where('booked', false);
            } elseif ($status == 'booked') {
                $slotsQuery->where('booked', true);
            } elseif ($status == 'pending') {
                $slotsQuery->whereNull('booked');
            }
        }
    
        $slots = $slotsQuery->get();
    
        return view('students.appointments.slots', compact('supervisor', 'slots', 'projects'));
    }
    

    public function editModal(Slot $slot)
    {
        if ($slot->supervisor_id != Auth::id()) {
            abort(403);
        }
       
        return response()->json($slot); // Return slot data as JSON
    }
    

    public function update(Request $request, Slot $slot)
    {
        if ($slot->supervisor_id != Auth::id()) {
            abort(403);
        }
    
        Log::info('Update Slot Request Data:', $request->all());
    
        $validatedData = $request->validate([
            'date' => ['required', 'date', function ($attribute, $value, $fail) {
                if (Carbon::parse($value)->isBefore(Carbon::today())) {
                    $fail('The selected date is in the past.');
                }
            }],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'meeting_details' => 'nullable|string|max:255',
            'repeat_weeks' => 'nullable|integer|min:1|max:52',
            'project_id' => 'required|exists:projects,id',
        ]);
    
        Log::info('Validated Data for Slot Update:', $validatedData);
    
        // Update the primary slot
        $slot->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'meeting_details' => $request->meeting_details,
            'repeat_weeks' => $request->repeat_weeks,
            'project_id' => $request->project_id,
        ]);
    
        // Handle repeat slots
        if ($request->repeat_weeks) {
            // Clear existing repeat slots for this main slot
            Slot::where('supervisor_id', $slot->supervisor_id)
                ->where('project_id', $slot->project_id)
                ->where('id', '!=', $slot->id) // Ensure not deleting the primary slot
                ->delete();
    
            // Recreate the repeat slots
            $currentDate = Carbon::parse($request->date);
            for ($i = 1; $i <= $request->repeat_weeks; $i++) {
                Slot::create([
                    'supervisor_id' => $slot->supervisor_id,
                    'project_id' => $request->project_id,
                    'date' => $currentDate->copy()->addWeeks($i),
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'meeting_details' => $request->meeting_details,
                ]);
            }
        }
    
        Log::info('Slot Updated Successfully:', $slot->toArray());
    
        return redirect()->route('slots.index')->with('success', 'Slot updated successfully.');
    }
    
}
