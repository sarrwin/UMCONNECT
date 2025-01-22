@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="dashboardTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="announcement-tab" data-bs-toggle="tab" href="#announcements" role="tab" aria-controls="announcements" aria-selected="true">
                <i class="bi bi-megaphone"></i> General Announcements
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="rooms-tab" data-bs-toggle="tab" href="#rooms" role="tab" aria-controls="rooms" aria-selected="false">
                <i class="bi bi-chat-dots"></i> Project Chat Rooms
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="dashboardTabContent">
        <!-- General Announcements Tab -->
        <div class="tab-pane fade show active" id="announcements" role="tabpanel" aria-labelledby="announcement-tab">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-broadcast"></i> Supervisor's Announcements
                </div>
                <div class="card-body bg-light" style="border-radius: 0 0 10px 10px;">
                    <div class="list-group" id="announcement-list" style="max-height: 500px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 10px;">
                        @if($announcementRoom && $announcements->count())
                            <h5 class="mb-3 fw-bold text-secondary">Recent Announcements:</h5>
                            @foreach($announcements as $announcement)
                            <div class="list-group-item d-flex align-items-start bg-white mb-3 rounded shadow-sm p-3">
    <!-- Profile Picture (Clickable Link) -->
    <a href="{{ route('supervisor.profile.show', $announcement->user->id) }}"  class="me-3 flex-shrink-0">
        <img src="{{ $announcement->user->profile_picture ? asset('uploads/' . $announcement->user->profile_picture) : asset('profile-placeholder.png') }}" 
             alt="{{ $announcement->user->name }}" 
             class="rounded-circle shadow-sm" 
             style="width: 50px; height: 50px; object-fit: cover;">
    </a>

    <!-- Message Content -->
    <div>
        <strong class="text-primary">
            <a href="{{ route('supervisor.profile.show', $announcement->user->id) }}" class="text-decoration-none text-primary">
                {{ $announcement->user->name }}
            </a>
        </strong>
        <p class="mb-1 text-dark">{{ $announcement->content }}</p>
        <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
    </div>
</div>

                            @endforeach
                        @else
                            <p class="text-muted text-center">No announcements available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Chat Rooms Tab -->
        <div class="tab-pane fade" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">
            <div class="row justify-content-center">
                @foreach($projectRooms as $room)
                    <div class="col-md-8 mb-4">
                        <div class="card shadow-lg border-0 h-100">
                            <div class="card-header bg-primary text-white fw-bold">
                                <i class="bi bi-chat-left-dots"></i> {{ $room->title }}
                            </div>
                            <div class="card-body bg-light p-4" style="border-radius: 0 0 10px 10px;">
                                <!-- Chat Messages -->
                                <div id="chat-messages-{{ $room->id }}" class="chat-messages mb-3" style="max-height: 400px; overflow-y: auto; background: #ffffff; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                                    <p class="text-muted text-center">Loading messages...</p>
                                </div>

                                <!-- Input Form to Send Message -->
                                <form class="send-message-form" data-room-id="{{ $room->id }}" method="POST">
    @csrf
    <div class="input-group">
        <input 
            type="text" 
            name="message" 
            class="form-control rounded-start" 
            placeholder="Type a message..." 
            required 
            {{ $room->is_disabled ? 'disabled' : '' }}>
        
        <button 
            type="submit" 
            class="btn btn-primary" 
            {{ $room->is_disabled ? 'disabled' : '' }}>
            <i class="bi bi-send"></i> Send
        </button>
    </div>
    @if($room->is_disabled)
        <p class="text-danger mt-2">This chat room is currently disabled by the supervisor.</p>
    @endif
</form>

                                
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
$(document).ready(function() {
    const POLLING_INTERVAL = 5000;
    let isEditing = false;

      // Function to notify the server that the user is active
      const notifyServer = (roomId) => {
        $.post('/chatroom/active', {
            _token: "{{ csrf_token() }}",
            room_id: roomId,
            active: true
        });
    };

    // Notify server for all chat rooms on page load
    @foreach($projectRooms as $room)
        notifyServer({{ $room->id }});
    @endforeach

    // Notify server every 30 seconds
    const activityIntervals = [];
    @foreach($projectRooms as $room)
        const intervalId = setInterval(() => {
            notifyServer({{ $room->id }});
        }, 30000); // Every 30 seconds
        activityIntervals.push(intervalId);
    @endforeach

    // Stop notifying when user leaves the page
    $(window).on('beforeunload', function() {
        @foreach($projectRooms as $room)
            $.post('/chatroom/active', {
                _token: "{{ csrf_token() }}",
                room_id: {{ $room->id }},
                active: false
            });
        @endforeach

        // Clear all intervals
        activityIntervals.forEach(intervalId => clearInterval(intervalId));
    })

    // Load messages for each room
    function loadRoomMessages(roomId) {
        if (isEditing) return;
        $.ajax({
            url: `/chatroom/messages/${roomId}`,
            method: 'GET',
            success: function(messages) {
                let chatBox = $(`#chat-messages-${roomId}`);
                chatBox.empty();

                messages.forEach(function(message) {
                    let messageTime = new Date(message.created_at).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });

                    let authId = "{{ Auth::id() }}";

                    let profileUrl = authId == message.user.id 
                        ? "/students/profile/edit" 
                        : `/students/profiles/${message.user.id}`;

                    let profilePicture = message.user.profile_picture 
                        ? `/uploads/${message.user.profile_picture}` 
                        : '/profile-placeholder.png';

                        chatBox.append(`
    <div id="message-${message.id}" class="d-flex align-items-center mb-3 position-relative">
        <!-- Profile Picture -->
        <a href="${profileUrl}" class="me-3">
            <img src="${profilePicture}" 
                 alt="${message.user.name}" 
                 class="rounded-circle" 
                 style="width: 40px; height: 40px; object-fit: cover;">
        </a>
        <!-- Message Content -->
        <div>
            <strong>
                <a href="/students/profile/show/${message.user.id}" class="text-decoration-none">
                    ${message.user.name}
                </a>
            </strong>: 
            <span id="message-content-${message.id}">${message.content}</span>
            <input type="text" id="edit-input-${message.id}" class="form-control d-none" value="${message.content}" style="display: inline-block; width: 80%;">
            <br>
            <small class="text-muted">${messageTime}</small>
        </div>
        <!-- Action Icons -->
        ${authId == message.user.id 
            ? `<div class="position-absolute top-0 end-0 me-3 mt-2">
                   <button class="btn btn-sm btn-light edit-message" data-message-id="${message.id}" title="Edit">
                       <i class="fas fa-edit"></i>
                   </button>
                   <button class="btn btn-sm btn-success save-message d-none" data-message-id="${message.id}" title="Save">
                       <i class="fas fa-save"></i>
                   </button>
                   <button class="btn btn-sm btn-danger delete-message" data-message-id="${message.id}" title="Delete">
                       <i class="fas fa-trash-alt"></i>
                   </button>
               </div>`
            : ''}
    </div>
`);
                });

                chatBox.scrollTop(chatBox[0].scrollHeight);
            },
            error: function(xhr) {
                console.error('Error fetching messages for room ' + roomId + ':', xhr.responseText);
            }
        });
    }

    setInterval(function() {
        @foreach($projectRooms as $room)
            loadRoomMessages({{ $room->id }});
        @endforeach
    }, POLLING_INTERVAL);

    $('.send-message-form').submit(function(e) {
        e.preventDefault();

        let roomId = $(this).data('room-id');
        let messageInput = $(this).find('input[name="message"]');
        let message = messageInput.val();

        $.ajax({
            url: `/chatroom/send-message/${roomId}`,
            method: 'POST',
            data: {
                message: message,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                messageInput.val('');
                loadRoomMessages(roomId);
            },
            error: function(xhr) {
                console.error('Error sending message:', xhr.responseText);
            }
        });
    });

    // Edit message
    $(document).on('click', '.edit-message', function() {
        isEditing = true; 
        let messageId = $(this).data('message-id');
        $(`#message-content-${messageId}`).addClass('d-none');
        $(`#edit-input-${messageId}`).removeClass('d-none').focus();
        $(this).addClass('d-none');
        $(`.save-message[data-message-id="${messageId}"]`).removeClass('d-none');
    });

    // Save edited message
    $(document).on('click', '.save-message', function() {
        let messageId = $(this).data('message-id');
        let newContent = $(`#edit-input-${messageId}`).val();

        $.ajax({
            url: `/chatroom/messages/${messageId}`,
            method: 'PATCH',
            data: {
                content: newContent,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $(`#message-content-${messageId}`).text(response.data.content).removeClass('d-none');
                $(`#edit-input-${messageId}`).addClass('d-none');
                $(`.edit-message[data-message-id="${messageId}"]`).removeClass('d-none');
                $(`.save-message[data-message-id="${messageId}"]`).addClass('d-none');
                isEditing = false; 
            },
            error: function(xhr) {
                console.error('Error saving message:', xhr.responseText);
                isEditing = false;
            }
        });
    });

    
    $(document).on('click', '.delete-message', function() {
    if (!confirm("Are you sure you want to delete this message?")) return;

    let messageId = $(this).data('message-id');

    $.ajax({
        url: `/chatroom/messages/${messageId}`,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $(`#message-${messageId}`).remove(); // Remove the message element from the DOM
            console.log(response.success);
        },
        error: function(xhr) {
            console.error('Error deleting message:', xhr.responseText);
        }
    });
});
});

</script>

<style>
    .card:hover {
    transform: none !important; /* Prevents moving up */
    box-shadow: none !important; /* Prevents shadow change */
}
</style>
@endsection
