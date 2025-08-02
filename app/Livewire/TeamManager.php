<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class TeamManager extends Component
{
    use WithPagination;

    public $activeTab = 'teams';
    public $search = '';
    public $showCreateModal = false;
    public $showMemberModal = false;
    public $selectedTeam = null;
    
    // Team creation properties
    public $name = '';
    public $description = '';
    
    // Member management properties
    public $userId = null;
    public $role = 'member';
    
    protected $queryString = ['search', 'activeTab'];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
    ];

    public function mount($action = null, $team = null)
    {
        if ($action === 'create') {
            $this->showCreateModal = true;
        }
        
        if ($team) {
            $this->selectedTeam = $team;
            $this->activeTab = 'members';
        }
    }

    public function showCreateModal()
    {
        $this->showCreateModal = true;
        $this->resetForm();
    }

    public function hideCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->resetErrorBag();
    }

    public function createTeam()
    {
        $this->validate();

        try {
            $team = Team::create([
                'name' => $this->name,
                'description' => $this->description,
                'created_by' => auth()->id(),
            ]);

            // Add creator as team leader
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => auth()->id(),
                'role' => 'leader',
                'joined_at' => now(),
            ]);

            session()->flash('message', 'Team created successfully!');
            $this->hideCreateModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create team: ' . $e->getMessage());
        }
    }

    public function selectTeam($teamId)
    {
        $this->selectedTeam = $teamId;
        $this->activeTab = 'members';
    }

    public function showMemberModal($teamId)
    {
        $this->selectedTeam = $teamId;
        $this->showMemberModal = true;
        $this->userId = null;
        $this->role = 'member';
    }

    public function hideMemberModal()
    {
        $this->showMemberModal = false;
        $this->userId = null;
        $this->role = 'member';
    }

    public function addMember()
    {
        $this->validate([
            'userId' => 'required|exists:users,id',
            'role' => 'required|in:member,moderator,leader',
        ]);

        try {
            // Check if user is already a member
            $existingMember = TeamMember::where('team_id', $this->selectedTeam)
                                      ->where('user_id', $this->userId)
                                      ->first();

            if ($existingMember) {
                session()->flash('error', 'User is already a member of this team.');
                return;
            }

            TeamMember::create([
                'team_id' => $this->selectedTeam,
                'user_id' => $this->userId,
                'role' => $this->role,
                'joined_at' => now(),
            ]);

            // Create notification for the added user
            Notification::create([
                'user_id' => $this->userId,
                'type' => 'team_invitation',
                'title' => 'Added to Team',
                'message' => 'You have been added to a team.',
                'data' => json_encode(['team_id' => $this->selectedTeam]),
            ]);

            session()->flash('message', 'Member added successfully!');
            $this->hideMemberModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add member: ' . $e->getMessage());
        }
    }

    public function removeMember($memberId)
    {
        try {
            $member = TeamMember::findOrFail($memberId);
            
            // Create notification for the removed user
            Notification::create([
                'user_id' => $member->user_id,
                'type' => 'team_removal',
                'title' => 'Removed from Team',
                'message' => 'You have been removed from a team.',
                'data' => json_encode(['team_id' => $member->team_id]),
            ]);

            $member->delete();
            session()->flash('message', 'Member removed successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove member: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getTeamsProperty()
    {
        $query = Team::query()
            ->with(['members.user', 'creator'])
            ->withCount('members');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function getUsersProperty()
    {
        return User::where('id', '!=', auth()->id())
                   ->orderBy('name')
                   ->get();
    }

    public function getSelectedTeamDataProperty()
    {
        if (!$this->selectedTeam) {
            return null;
        }

        return Team::with(['members.user', 'creator'])
                   ->find($this->selectedTeam);
    }

    public function getNotificationsProperty()
    {
        return Notification::where('user_id', auth()->id())
                          ->where('read_at', null)
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
                                   ->where('user_id', auth()->id())
                                   ->first();

        if ($notification) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function render()
    {
        return view('livewire.team-manager', [
            'teams' => $this->teams,
            'users' => $this->users,
            'selectedTeamData' => $this->selectedTeamData,
            'notifications' => $this->notifications,
        ]);
    }
}