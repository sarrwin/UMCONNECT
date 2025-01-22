@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container">
    <div class="card shadow-lg border-0 mb-5">
        <!-- Header -->
        <div class="card-header text-white text-center fw-bold" style="background-color: #584f7a;" >
            <h2 class="mb-0">Your Slots</h2>
        </div>

        <!-- Card Body -->
        <div class="card-body">
    <!-- Filter and Create Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('slots.index') }}" class="d-flex align-items-center">
            <select name="filter" class="form-select me-2" style="width: 220px;">
                <option value="" selected>Select Filter</option>
                <option value="upcoming" {{ request('filter') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="past" {{ request('filter') === 'past' ? 'selected' : '' }}>Past</option>
                <option value="available" {{ request('filter') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="booked" {{ request('filter') === 'booked' ? 'selected' : '' }}>Booked</option>
                
            </select>
            <select name="project_id" class="form-select me-2" style="width: 220px;">
                <option value="">All Projects</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->title }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-primary px-3">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>

        <!-- Create Slot Button -->
        <button id="openModalButton" class="btn btn-success px-4 py-2">
            <i class="fas fa-plus-circle"></i> Create New Slot
        </button>
    </div>

    <!-- Slots Table -->
    @if($slots->isEmpty())
        <div class="alert alert-info text-center">
            <strong>No slots available.</strong> Please create new slots to manage your appointments.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Project</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $slot)
                        <tr class="text-center">
                            <td>{{ \Carbon\Carbon::parse($slot->date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</td>
                            <td>{{ $slot->project->title ?? 'General' }}</td>
                            <td>{{ $slot->meeting_details }}</td>
                            <td>
                            @if ($slot->booked === 1)
    <span class="badge bg-danger">Booked</span>
@elseif ($slot->booked === 0)
    <span class="badge bg-success">Available</span>
@elseif ($slot->booked === 2)
    <span class="badge bg-warning">Custom</span>
@endif
                            </td>
                            <td>
                                @if ($slot->booked)
                                    <a href="{{ route('appointments.upcoming', ['slotId' => $slot->id]) }}" class="btn btn-info btn-sm">View Details</a>
                                @else
                                <form action="{{ route('slots.destroy', $slot) }}" method="POST" class="d-inline" onsubmit="return confirmDelete()">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm"> <i class="fas fa-trash-alt"></i></button>
</form>
                                    <button class="btn btn-primary btn-sm edit-slot-button" 
                                        data-id="{{ $slot->id }}" 
                                        data-url="{{ route('slots.editModal', $slot->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-3">
            {{ $slots->appends(['filter' => request('filter'), 'project_id' => request('project_id')])->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>

    </div>

   <!-- Cards Section -->
<div class="row justify-content-center text-center mt-4">
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <i class="fas fa-history fa-4x text-warning mb-3"></i>
                <h5>Past Appointments</h5>
                <p class="text-muted">Check completed appointments.</p>
                <a href="{{ route('supervisor.appointments.past') }}" class="btn btn-outline-warning">View History</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow h-100">
            <div class="card-body">
                <i class="fas fa-tasks fa-4x text-danger mb-3"></i>
                <h5>Manage Requests</h5>
                <p class="text-muted">Accept or decline requests.</p>
                <a href="{{ route('supervisor.appointments.manage') }}" class="btn btn-outline-danger">Manage Requests</a>
            </div>
        </div>
    </div>
</div>

     <!-- Google Auth Modal -->
     <div class="modal fade" id="googleAuthModal" tabindex="-1" aria-labelledby="googleAuthModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="googleAuthModalLabel">Google Sign-In Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>To view the appointment details, please sign in to Google to sync with Google Calendar.</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('google.auth') }}" class="btn btn-primary" id="googleAuthButton">Sign in with Google</a>
                </div>
            </div>
        </div>
     </div>

        <!-- Custom Modal -->
<div id="customModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h5 class="modal-title">Create New Slot</h5>
            <button id="closeModalButton" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
            <form action="{{ route('slots.store') }}" method="POST" id="slotForm" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control">
                    <div class="invalid-feedback" id="dateError">The date is required and must not be in the past.</div>
                </div>

                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control">
                    <div class="invalid-feedback" id="startTimeError">Start time is required.</div>
                </div>

                <div class="mb-3">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control">
                    <div class="invalid-feedback" id="endTimeError">End time is required and must be after start time.</div>
                </div>

                <div class="mb-3">
                    <label for="meeting_details" class="form-label">Meeting Details</label>
                    <input 
                        type="text" 
                        name="meeting_details" 
                        id="meeting_details" 
                        class="form-control">
                    <div class="invalid-feedback" id="meetingDetailsError">Meeting details cannot exceed 255 characters.</div>
                </div>

                <div class="mb-3">
    <label for="repeat_interval" class="form-label">Repeat Interval</label>
    <select name="repeat_interval" id="repeat_interval" class="form-control">
        <option value="">Do not repeat</option>
        <option value="daily">Every Day</option>
        <option value="weekly">Every Week</option>
        <option value="biweekly">Every 2 Weeks</option>
        <option value="custom">Custom (Specify Weeks)</option>
    </select>
</div>

<div class="mb-3" id="customRepeatWrapper" style="display: none;">
    <label for="repeat_weeks" class="form-label">Repeat Every (Weeks)</label>
    <input type="number" name="repeat_weeks" id="repeat_weeks" class="form-control">
</div>

<div class="mb-3">
    <label for="repeat_end_date" class="form-label">Repeat Until</label>
    <input type="date" name="repeat_end_date" id="repeat_end_date" class="form-control">
</div>

                <div class="mb-3">
                    <label for="project_id" class="form-label">Project</label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value="">Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="projectError">Please select a project.</div>
                </div>

                <div class="text-end">
                    <button type="button" id="cancelModalButton" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Slot Modal -->
<div id="editSlotModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h5 class="modal-title">Edit Slot</h5>
            <button id="closeEditModalButton" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editSlotForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="slot_id" id="slot_id">

                <div class="mb-3">
                    <label for="edit_date" class="form-label">Date</label>
                    <input type="date" name="date" id="edit_date" class="form-control" required>
                    <div class="invalid-feedback">The date is required and must not be in the past.</div>
                </div>

                <div class="mb-3">
                    <label for="edit_start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                    <div class="invalid-feedback">Start time is required.</div>
                </div>

                <div class="mb-3">
                    <label for="edit_end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                    <div class="invalid-feedback">End time is required and must be after start time.</div>
                </div>

                <div class="mb-3">
                    <label for="edit_meeting_details" class="form-label">Meeting Details</label>
                    <input type="text" name="meeting_details" id="edit_meeting_details" class="form-control" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="edit_repeat_weeks" class="form-label">Repeat Weekly For (Weeks)</label>
                    <input type="number" name="repeat_weeks" id="edit_repeat_weeks" class="form-control" min="1" max="52">
                </div>

                <div class="mb-3">
                    <label for="edit_project_id" class="form-label">Project</label>
                    <select name="project_id" id="edit_project_id" class="form-control">
                        <option value="">Select a project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-end">
                    <button type="button" id="cancelEditModalButton" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>





    </div>
    
    <script>
    // Check session flag for showing the Google Auth modal
    let googleAuthShown = {{ session('google_auth_shown') ? 'true' : 'false' }};
    console.log('Initial googleAuthShown:', googleAuthShown);

    function handleViewDetailsClick(event) {
        if (!googleAuthShown) {
            console.log('Google Auth modal will be shown.');
            event.preventDefault(); // Prevent navigation on first click
            googleAuthShown = true;

            // Update the session using an AJAX call
            fetch("{{ route('session.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ google_auth_shown: true })
            }).then(response => {
                if (response.ok) {
                    console.log('Session updated successfully.');
                } else {
                    console.error('Failed to update session.');
                }
            });

            // Show the Google Auth modal
            const modal = new bootstrap.Modal(document.getElementById('googleAuthModal'));
            modal.show();
        } else {
            console.log('Google Auth modal already shown, navigating directly.');
        }
    }

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Modal Elements
    const openModalButton = document.getElementById("openModalButton");
    const closeModalButton = document.getElementById("closeModalButton");
    const cancelModalButton = document.getElementById("cancelModalButton");
    const modalOverlay = document.getElementById("customModal");

    // Form Validation Elements
    const form = document.getElementById("slotForm");
    const dateField = document.getElementById("date");
    const startTimeField = document.getElementById("start_time");
    const endTimeField = document.getElementById("end_time");
    const meetingDetailsField = document.getElementById("meeting_details");
    const repeatWeeksField = document.getElementById("repeat_weeks");
    const projectField = document.getElementById("project_id");

    const dateError = document.getElementById("dateError");
    const startTimeError = document.getElementById("startTimeError");
    const endTimeError = document.getElementById("endTimeError");
    const meetingDetailsError = document.getElementById("meetingDetailsError");
    const repeatWeeksError = document.getElementById("repeatWeeksError");
    const projectError = document.getElementById("projectError");

    // Open Modal
    openModalButton.addEventListener("click", function () {
        modalOverlay.classList.add("show");
    });

    // Close Modal
    closeModalButton.addEventListener("click", closeModal);
    cancelModalButton.addEventListener("click", closeModal);
    modalOverlay.addEventListener("click", function (event) {
        if (event.target === modalOverlay) {
            closeModal();
        }
    });
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && modalOverlay.classList.contains("show")) {
            closeModal();
        }
    });

    function closeModal() {
        modalOverlay.classList.remove("show");
    }

    // Real-Time Validation
    form.addEventListener("input", function (event) {
        validateField(event.target);
    });

    // Validate Form on Submit
    form.addEventListener("submit", function (event) {
        let isValid = validateForm();
        if (!isValid) {
            event.preventDefault(); // Prevent submission if validation fails
        }
    });

    // Form Validation Logic
    function validateForm() {
        let isValid = true;

        // Validate Date
        if (!dateField.value || new Date(dateField.value) < new Date().setHours(0, 0, 0, 0)) {
            dateError.style.display = "block";
            dateField.classList.add("is-invalid");
            isValid = false;
        } else {
            dateError.style.display = "none";
            dateField.classList.remove("is-invalid");
        }

        // Validate Start Time
        if (!startTimeField.value) {
            startTimeError.style.display = "block";
            startTimeField.classList.add("is-invalid");
            isValid = false;
        } else {
            startTimeError.style.display = "none";
            startTimeField.classList.remove("is-invalid");
        }

        // Validate End Time
        if (!endTimeField.value || endTimeField.value <= startTimeField.value) {
            endTimeError.style.display = "block";
            endTimeField.classList.add("is-invalid");
            isValid = false;
        } else {
            endTimeError.style.display = "none";
            endTimeField.classList.remove("is-invalid");
        }

        // Validate Meeting Details
        if (meetingDetailsField.value && meetingDetailsField.value.length < 0) {
            meetingDetailsError.style.display = "block";
            meetingDetailsField.classList.add("is-invalid");
            isValid = false;
        } else {
            meetingDetailsError.style.display = "none";
            meetingDetailsField.classList.remove("is-invalid");
        }

        // Validate Repeat Weeks
        if (repeatWeeksField.value && (repeatWeeksField.value < 1 || repeatWeeksField.value > 52)) {
            repeatWeeksError.style.display = "block";
            repeatWeeksField.classList.add("is-invalid");
            isValid = false;
        } else {
            repeatWeeksError.style.display = "none";
            repeatWeeksField.classList.remove("is-invalid");
        }

        // Validate Project Selection
        if (!projectField.value) {
            projectError.style.display = "block";
            projectField.classList.add("is-invalid");
            isValid = false;
        } else {
            projectError.style.display = "none";
            projectField.classList.remove("is-invalid");
        }

        return isValid;
    }

    // Real-Time Field Validation Logic
    function validateField(field) {
        if (field === dateField) {
            if (!field.value || new Date(field.value) < new Date().setHours(0, 0, 0, 0)) {
                dateError.style.display = "block";
                field.classList.add("is-invalid");
            } else {
                dateError.style.display = "none";
                field.classList.remove("is-invalid");
            }
        }

        if (field === startTimeField) {
            if (!field.value) {
                startTimeError.style.display = "block";
                field.classList.add("is-invalid");
            } else {
                startTimeError.style.display = "none";
                field.classList.remove("is-invalid");
            }
        }

        if (field === endTimeField) {
            if (!field.value || endTimeField.value <= startTimeField.value) {
                endTimeError.style.display = "block";
                field.classList.add("is-invalid");
            } else {
                endTimeError.style.display = "none";
                field.classList.remove("is-invalid");
            }
        }

        if (field === meetingDetailsField) {
            if (field.value && field.value.length > 255) {
                meetingDetailsError.style.display = "block";
                field.classList.add("is-invalid");
            } else {
                meetingDetailsError.style.display = "none";
                field.classList.remove("is-invalid");
            }
        }

        if (field === repeatWeeksField) {
            if (field.value && (field.value < 1 || field.value > 52)) {
                repeatWeeksError.style.display = "block";
                field.classList.add("is-invalid");
            } else {
                repeatWeeksError.style.display = "none";
                field.classList.remove("is-invalid");
            }
        }

        if (field === projectField) {
            if (!field.value) {
                projectError.style.display = "block";
                field.classList.add("is-invalid");
            } else {
                projectError.style.display = "none";
                field.classList.remove("is-invalid");
            }
        }
    }
});

document.getElementById('repeat_interval').addEventListener('change', function () {
    const customRepeatWrapper = document.getElementById('customRepeatWrapper');
    if (this.value === 'custom') {
        customRepeatWrapper.style.display = 'block';
    } else {
        customRepeatWrapper.style.display = 'none';
    }
});

</script>
<script>

document.addEventListener("DOMContentLoaded", function () {
    const editSlotModal = document.getElementById("editSlotModal");
    const closeEditModalButton = document.getElementById("closeEditModalButton");
    const cancelEditModalButton = document.getElementById("cancelEditModalButton");
    const editSlotForm = document.getElementById("editSlotForm");

    // Open Edit Modal
    document.querySelectorAll(".edit-slot-button").forEach(button => {
        button.addEventListener("click", function () {
            const slotId = this.getAttribute("data-id");
            const fetchUrl = this.getAttribute("data-url");

            // Fetch slot data
            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) throw new Error("Failed to fetch slot data");
                    return response.json();
                })
                .then(slot => {
    console.log("Slot data fetched:", slot);
    const formattedDate = new Date(slot.date).toISOString().split('T')[0];
    const startTime = slot.start_time ? slot.start_time.substring(0, 5) : "";
    const endTime = slot.end_time ? slot.end_time.substring(0, 5) : "";

    // Populate the form fields
    editSlotForm.action = `/slots/${slot.id}`;
    document.getElementById("slot_id").value = slot.id;
    document.getElementById("edit_date").value = formattedDate;
    document.getElementById("edit_start_time").value = startTime;
    document.getElementById("edit_end_time").value = endTime;
    document.getElementById("edit_meeting_details").value = slot.meeting_details;
    document.getElementById("edit_repeat_weeks").value = slot.repeat_weeks || ""; // Populate repeat weeks
    document.getElementById("edit_project_id").value = slot.project_id;

    // Show the modal
    editSlotModal.classList.add("show");
})
                .catch(error => console.error("Error fetching slot data:", error));
        });
    });

    // Close Edit Modal
    function closeEditModal() {
        editSlotModal.classList.remove("show");
    }
    closeEditModalButton.addEventListener("click", closeEditModal);
    cancelEditModalButton.addEventListener("click", closeEditModal);

    // Clear modal on close
    editSlotModal.addEventListener("click", event => {
        if (event.target === editSlotModal) closeEditModal();
    });
})

function confirmDelete() {
        return confirm("Are you sure you want to delete this slot? This action cannot be undone.");
    }
</script>

<style>

/* Modal Overlay */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

/* Modal Container */
.modal-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 50%;
    max-width: 600px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

/* Modal Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    margin-bottom: 15px;
}

/* Close Button */
.close-button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

/* Show Modal */
.modal-overlay.show {
    display: flex;
}



</style>
@endsection
