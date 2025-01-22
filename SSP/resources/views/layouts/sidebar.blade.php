<div :class="{ 'block': open, 'hidden': !open }" 
@class="console.log('Sidebar class changed:', open)" class="sidebar bg-[#D5C4F3] w-64 fixed h-screen border-gray-100 transform transition-transform">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-4 py-4 border-b border-gray-100">
        <!-- Logo -->
        <div class="flex items-center">
            @if (Auth::user()->isStudent())
                <x-nav-link :href="route('students.dashboard')" :active="request()->routeIs('student.dashboard')">
                    <img src="{{ asset('image.png') }}" class="block h-9 w-auto" alt="Application Logo">
                </x-nav-link>
            @elseif (Auth::user()->isSupervisor())
                <x-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')">
                    <img src="{{ asset('image.png') }}" class="block h-9 w-auto" alt="Application Logo">
                </x-nav-link>
            @elseif (Auth::user()->isAdmin())
                <x-nav-link :href="route('admin.index')" :active="request()->routeIs('coordinator.dashboard')">
                    <img src="{{ asset('image.png') }}" class="block h-9 w-auto" alt="Application Logo">
                </x-nav-link>
            @endif
        </div>
        
        <button @click="console.log('Toggling Sidebar:', open); open = !open" class="flex items-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
        
    </div>

    <!-- Sidebar Content -->
  
    <!-- Sidebar Content -->
    <!-- Notifications List -->
    <div class="p-4 h-[calc(100vh-120px)] overflow-y-auto"> <!-- Adjust height for header and footer -->
        <form action="{{ route('notifications.markAsRead') }}" method="POST">
            @csrf
            <ul class="space-y-4">
                @forelse(auth()->user()->unreadNotifications as $notification)
                    <li class="p-2 border-b border-gray-700  text-white">
                        {{ $notification->data['message'] ?? 'You have a new notification' }}
                    </li>
                @empty
                    <li class="p-2 text-gray-400">No new notifications</li>
                @endforelse
            </ul>
            <div class="mt-4 text-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Mark all as read
                </button>
            </div>
        </form>
    </div>


    <!-- Sidebar Footer -->
    <div class="px-4 py-4 border-t border-gray-100">
        <div class="text-sm text-gray-500">
            UMCONNECT @2024
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let googleAuthShown = {{ session('google_auth_shown') ? 'true' : 'false' }};
        const userRole = "{{ Auth::user()->role ?? '' }}"; // Get the current user's role

        console.log('Initial googleAuthShown:', googleAuthShown);
        console.log('User Role:', userRole);

        const appointmentsLink = document.getElementById('appointmentsNavLink');
        appointmentsLink.addEventListener('click', function (event) {
            if (!googleAuthShown) {
                console.log('Google Auth modal will be shown.');
                event.preventDefault(); // Prevent navigation

                // Show the Google Auth modal
                const modal = new bootstrap.Modal(document.getElementById('googleAuthModal'));
                modal.show();

                // Update session to prevent showing the modal again
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
                        googleAuthShown = true; // Update the flag locally
                    } else {
                        console.error('Failed to update session.');
                    }
                });
            } else {
                console.log('Google Auth modal already shown, navigating directly.');
                // Navigate based on the user's role
                if (userRole === 'supervisor') {
                    window.location.href = "{{ route('slots.index') }}";
                } else {
                    window.location.href = "{{ route('students.appointments.index') }}";
                }
            }
        });
    });
</script>
<script>
   document.addEventListener('alpine:init', () => {
    Alpine.data('sidebar', () => ({
        open: localStorage.getItem('sidebarOpen') === 'true',
        toggleSidebar() {
            this.open = !this.open;
            localStorage.setItem('sidebarOpen', this.open);
        },
        handleNavClick(url) {
            this.open = true; // Ensure sidebar remains open
            localStorage.setItem('sidebarOpen', this.open); // Save state
            window.location.href = url; // Navigate to the URL
        }
    }));
});



</script>


<style>
.sidebar {
    transform: translateX(-90%);
    transition: transform 0.3s ease-in-out;
}
.sidebar.block {
    transform: translateX(0);
}
</style>
