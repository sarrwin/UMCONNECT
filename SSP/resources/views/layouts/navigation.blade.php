<nav class="bg-[#584f7a] border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Section: Hamburger and Navigation Links -->
            <div class="flex items-center">
                <!-- Hamburger Menu -->
                <button 
        @click="open = true" 
        class="flex items-center text-white relative bg-gray-800 p-2 rounded-full shadow-md hover:bg-gray-700">
        <i class="fas fa-bell"></i>
        @if(auth()->user()->unreadNotifications->count())
            <span class="absolute top-0 right-0 inline-block w-5 h-5 bg-red-500 text-white text-xs rounded-full text-center">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

                <!-- Role-Specific Navigation Links -->
                <nav class="flex ml-4 space-x-4">
                    @if (Auth::check() && Auth::user()->isStudent())
                        <x-nav-link :href="route('students.dashboard')" :active="request()->routeIs('students.dashboard')" class="text-white hover:text-blue-200">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link id="appointmentsNavLink" href="javascript:void(0)" class="text-white hover:text-blue-200">
    {{ __('Appointments') }}
</x-nav-link>

                        <x-nav-link :href="route('students.projects.index_all')" :active="request()->routeIs('students.projects.index_all')" class="text-white hover:text-blue-200">
                            {{ __('Projects') }}
                        </x-nav-link>
                        <x-nav-link :href="route('students.projects.my_project')" :active="request()->routeIs('students.projects.my_project')" class="text-white hover:text-blue-200">
                            {{ __('My Project') }}
                        </x-nav-link>
                        <x-nav-link :href="route('students.logbook.index')" :active="request()->routeIs('students.logbook.index')" class="text-white hover:text-blue-200">
                            {{ __('Logbook') }}
                        </x-nav-link>
                    @elseif (Auth::check() && Auth::user()->isSupervisor())
                        <x-nav-link :href="route('supervisor.dashboard')" :active="request()->routeIs('supervisor.dashboard')" class="text-white hover:text-blue-200">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link id="appointmentsNavLink" href="javascript:void(0)" class="text-white hover:text-blue-200">
    {{ __('Appointments') }}
</x-nav-link>

                        <x-nav-link :href="route('supervisor.students.projects.index')" :active="request()->routeIs('supervisor.students.projects.index')" class="text-white hover:text-blue-200">
                            {{ __('Students') }}
                        </x-nav-link>
                        <x-nav-link :href="route('supervisor.projects.index')" :active="request()->routeIs('supervisor.projects.index')" class="text-white hover:text-blue-200">
                            {{ __('Projects') }}
                        </x-nav-link>
                        @if (Auth::user()->supervisor && Auth::user()->supervisor->is_coordinator)
                            <x-nav-link :href="route('coordinator.dashboard')" :active="request()->routeIs('coordinator.dashboard')" class="text-white hover:text-blue-200">
                                {{ __('Coordinator Dashboard') }}
                            </x-nav-link>
                        @endif
                    @elseif (Auth::check() && Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" class="text-white hover:text-blue-200">
                            {{ __('Manage Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('feedback.index')" :active="request()->routeIs('feedback.index')" class="text-white hover:text-blue-200">
                            {{ __('Manage Feedback') }}
                        </x-nav-link>
                    @endif
                </nav>
            </div>

            <!-- Right Section: Notifications and User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Notification Bell -->
                <!-- <div class="dropdown me-3">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" id="notificationBell" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if(auth()->user()->unreadNotifications->count())
                            <span class="badge badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationBell">
                        <form action="{{ route('notifications.markAsRead') }}" method="POST">
                            @csrf
                            @foreach(auth()->user()->unreadNotifications as $notification)
                                <a class="dropdown-item">
                                    {{ $notification->data['message'] ?? 'You have a new notification' }}
                                </a>
                            @endforeach
                            <div class="dropdown-divider"></div>
                            <button type="submit" class="dropdown-item text-center">Mark all as read</button>
                        </form>
                    </div>
                </div> -->

               <!-- Feedback Icon -->
               <div class="me-3">
                    @if (Auth::user()->role === 'admin')
                        <a href="{{ route('feedback.index') }}" class="nav-link" title="Manage Feedback">
                        <i class='far fa-envelope-open' style='font-size:36px'></i>
                        </a>
                    @else
                        <a href="{{ route('feedback.create') }}" class="nav-link" title="Submit Feedback">
                        <i class='far fa-envelope-open text-Black' style='font-size:26px'></i>
                        </a>
                    @endif
                </div>

                <!-- User Dropdown -->
                <x-dropdown width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-[#584f7a] hover:bg-blue-600 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        @if (Auth::user()->isStudent())
                            <x-dropdown-link :href="route('students.profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @elseif (Auth::user()->isSupervisor())
                            <x-dropdown-link :href="route('supervisor.profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                         this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const googleAuthModal = new bootstrap.Modal(document.getElementById('googleAuthModal'));
        const appointmentsLink = document.getElementById('appointmentsNavLink');

        // Role-based route handling
        const userRole = "{{ Auth::user()->isSupervisor() ? 'supervisor' : 'student' }}";
        const appointmentsRoute = userRole === 'supervisor' 
            ? "{{ route('slots.index') }}" 
            : "{{ route('students.appointments.index') }}";

        // Check if the modal should be shown based on session
        const modalShown = "{{ session('google_auth_shown') }}" === "1";

        appointmentsLink?.addEventListener('click', function (event) {
            if (!modalShown) {
                event.preventDefault(); // Prevent navigation
                googleAuthModal.show(); // Show the modal

                // Update session to indicate the modal has been shown
                fetch("{{ route('session.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ google_auth_shown: true })
                });
            } else {
                // Redirect to the respective route directly
                window.location.href = appointmentsRoute;
            }
        });
    });









</script>
