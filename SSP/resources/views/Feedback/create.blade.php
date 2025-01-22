@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Card Wrapper -->
    <div class="card shadow-lg border-0">
        <!-- Card Header -->
        <div class="card-header text-white text-center py-3" style="background-color: #584f7a;">
            <h2 class="mb-0">📋 Feedback</h2>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <!-- Centered Toggle Tabs -->
            <ul class="nav nav-tabs justify-content-center" id="feedbackTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-dark px-4 py-2" id="submit-tab" data-bs-toggle="tab" data-bs-target="#submitFeedback" type="button" role="tab" aria-controls="submitFeedback" aria-selected="true">
                        Submit Feedback
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark px-4 py-2" id="status-tab" data-bs-toggle="tab" data-bs-target="#feedbackStatus" type="button" role="tab" aria-controls="feedbackStatus" aria-selected="false">
                        Feedback Status
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-4" id="feedbackTabsContent">
                <!-- Submit Feedback Tab -->
                <div class="tab-pane fade show active" id="submitFeedback" role="tabpanel" aria-labelledby="submit-tab">
                    <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="subject" class="form-label">📌 Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Enter feedback subject" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">✍️ Message</label>
                            <textarea id="message" name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Describe your feedback in detail" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="screenshot" class="form-label">📸 Screenshot (Optional)</label>
                            <input type="file" id="screenshot" name="screenshot" class="form-control @error('screenshot') is-invalid @enderror" accept="image/*">
                            @error('screenshot')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Submit Feedback</button>
                    </form>
                </div>

                <!-- Feedback Status Tab -->
                <div class="tab-pane fade" id="feedbackStatus" role="tabpanel" aria-labelledby="status-tab">
                    <h4 class="mb-4 text-center text-muted">📊 Your Feedback Status</h4>
                    @if($feedbacks->isEmpty())
                        <div class="alert alert-info text-center">
                            <strong>No feedback submitted yet.</strong>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Screenshot</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedbacks as $feedback)
                                        <tr>
                                            <td>{{ $feedback->subject }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($feedback->message, 50, '...') }}</td>
                                            <td class="text-center">
                                                @if($feedback->screenshot)
                                                    <a href="{{ asset('storage/' . $feedback->screenshot) }}" target="_blank" class="btn btn-sm btn-info">View</a>
                                                @else
                                                    <span class="text-muted">No Screenshot</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge 
                                                    @if($feedback->status === 'resolved') bg-success
                                                    @elseif($feedback->status === 'unresolved') bg-warning
                                                    @else bg-danger
                                                    @endif">
                                                    {{ ucfirst($feedback->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $feedback->created_at->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-muted text-center">
            Thank you for helping us improve! Your feedback is invaluable. 📝
        </div>
    </div>
</div>

<style>
    .card-header {
        font-size: 1.5rem;
        font-weight: bold;
        background-color: #584f7a;
        color: white;
    }

    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        color: #555;
        font-weight: 600;
        border: none;
        background-color: #f0f0f0;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active,
    .nav-tabs .nav-link:hover {
        color: #fff;
        background: linear-gradient(135deg, #4C0865, #6E3C9E);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn {
        font-size: 0.95rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5em 0.75em;
        border-radius: 10px;
    }
</style>
@endsection
