<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Team Collaboration</h1>
                <div class="flex items-center space-x-3">
                    <!-- Notifications Bell -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 12L21 2l-9 10.5z"/>
                            </svg>
                            @if($notifications->count() > 0)
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                            @endif
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition 
                             class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Notifications</h3>
                                @if($notifications->count() > 0)
                                    <div class="space-y-2 max-h-64 overflow-y-auto">
                                        @foreach($notifications as $notification)
                                            <div class="p-3 bg-blue-50 rounded-md">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-medium text-blue-900">{{ $notification->title }}</h4>
                                                    <button wire:click="markNotificationAsRead({{ $notification->id }})" 
                                                            class="text-blue-600 hover:text-blue-800 text-xs">
                                                        Mark Read
                                                    </button>
                                                </div>
                                                <p class="text-sm text-blue-700 mt-1">{{ $notification->message }}</p>
                                                <p class="text-xs text-blue-600 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No new notifications</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <button wire:click="showCreateModal" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Team
                    </button>
                </div>
            </div>
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <div class="w-64 border-r border-gray-200">
                <nav class="p-4 space-y-2">
                    <a wire:click="setActiveTab('teams')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'teams' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        👥 All Teams
                    </a>
                    <a wire:click="setActiveTab('my-teams')" 
                       class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'my-teams' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        🏠 My Teams
                    </a>
                    @if($selectedTeamData)
                        <a wire:click="setActiveTab('members')" 
                           class="block px-3 py-2 rounded-md text-sm font-medium cursor-pointer {{ $activeTab === 'members' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            👤 Team Members
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Search Bar -->
                <div class="p-4 border-b border-gray-200">
                    <input type="text" wire:model.live="search" placeholder="Search teams..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="p-6">
                    @if($activeTab === 'teams' || $activeTab === 'my-teams')
                        <!-- Teams Grid -->
                        @if($teams->count() > 0)
                            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                @foreach($teams as $team)
                                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer"
                                         wire:click="selectTeam({{ $team->id }})">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $team->name }}</h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $team->members_count }} {{ Str::plural('member', $team->members_count) }}
                                            </span>
                                        </div>
                                        
                                        @if($team->description)
                                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($team->description, 100) }}</p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <img class="h-6 w-6 rounded-full" 
                                                     src="https://ui-avatars.com/api/?name={{ urlencode($team->creator->name) }}&color=7F9CF5&background=EBF4FF" 
                                                     alt="{{ $team->creator->name }}">
                                                <span class="text-xs text-gray-500">by {{ $team->creator->name }}</span>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $team->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <div class="mt-4 flex space-x-2">
                                            <button wire:click.stop="selectTeam({{ $team->id }})" 
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                View Details
                                            </button>
                                            <button wire:click.stop="showMemberModal({{ $team->id }})" 
                                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Add Member
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $teams->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No teams found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating your first team.</p>
                            </div>
                        @endif
                    @elseif($activeTab === 'members' && $selectedTeamData)
                        <!-- Team Members View -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">{{ $selectedTeamData->name }}</h2>
                                    @if($selectedTeamData->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $selectedTeamData->description }}</p>
                                    @endif
                                </div>
                                <button wire:click="showMemberModal({{ $selectedTeamData->id }})" 
                                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                    Add Member
                                </button>
                            </div>
                        </div>

                        @if($selectedTeamData->members->count() > 0)
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($selectedTeamData->members as $member)
                                        <li class="px-6 py-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <img class="h-10 w-10 rounded-full" 
                                                         src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&color=7F9CF5&background=EBF4FF" 
                                                         alt="{{ $member->user->name }}">
                                                    <div class="ml-4">
                                                        <div class="flex items-center space-x-2">
                                                            <h3 class="text-sm font-medium text-gray-900">{{ $member->user->name }}</h3>
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                {{ $member->role === 'leader' ? 'bg-purple-100 text-purple-800' : 
                                                                   ($member->role === 'moderator' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                                {{ ucfirst($member->role) }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-gray-500">{{ $member->user->email }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-500">Joined {{ $member->joined_at->diffForHumans() }}</span>
                                                    @if($member->user_id !== auth()->id())
                                                        <button wire:click="removeMember({{ $member->id }})" 
                                                                class="text-red-600 hover:text-red-800 text-sm">
                                                            Remove
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No members in this team yet.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Team Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Create New Team</h3>
                        <button wire:click="hideCreateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="createTeam" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Team Name</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                            <textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="hideCreateModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Create Team
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Member Modal -->
    @if($showMemberModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add Team Member</h3>
                        <button wire:click="hideMemberModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="addMember" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Select User</label>
                            <select wire:model="userId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Choose a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('userId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <select wire:model="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="member">Member</option>
                                <option value="moderator">Moderator</option>
                                <option value="leader">Leader</option>
                            </select>
                            @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="hideMemberModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Add Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" 
             class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" 
             class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>