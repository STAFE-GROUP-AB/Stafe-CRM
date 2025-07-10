<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LoyaltyProgram;
use App\Models\CustomerLoyaltyPoints;
use App\Models\Contact;

class LoyaltyProgramManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $editingProgram = null;
    public $name = '';
    public $description = '';
    public $tiers = [];
    public $pointRules = [];
    public $rewardsCatalog = [];
    public $isActive = true;
    public $startDate = '';
    public $endDate = '';

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'tiers' => 'required|array|min:1',
        'pointRules' => 'required|array|min:1',
        'rewardsCatalog' => 'required|array|min:1',
        'isActive' => 'boolean',
        'startDate' => 'nullable|date',
        'endDate' => 'nullable|date|after:start_date'
    ];

    public function render()
    {
        $programs = LoyaltyProgram::withCount('customerPoints')
            ->latest()
            ->paginate(10);

        $customerPoints = CustomerLoyaltyPoints::with(['contact', 'loyaltyProgram'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_programs' => LoyaltyProgram::count(),
            'active_programs' => LoyaltyProgram::active()->count(),
            'total_customers' => CustomerLoyaltyPoints::distinct('contact_id')->count(),
            'total_points_issued' => CustomerLoyaltyPoints::sum('total_points'),
            'total_points_redeemed' => CustomerLoyaltyPoints::sum('redeemed_points')
        ];

        return view('livewire.loyalty-program-manager', compact('programs', 'customerPoints', 'stats'));
    }

    public function createProgram()
    {
        $this->showCreateModal = true;
        $this->reset(['name', 'description', 'tiers', 'pointRules', 'rewardsCatalog', 'isActive', 'startDate', 'endDate']);
        $this->initializeDefaults();
    }

    public function editProgram($programId)
    {
        $program = LoyaltyProgram::findOrFail($programId);
        $this->editingProgram = $program;
        $this->name = $program->name;
        $this->description = $program->description;
        $this->tiers = $program->tiers;
        $this->pointRules = $program->point_rules;
        $this->rewardsCatalog = $program->rewards_catalog;
        $this->isActive = $program->is_active;
        $this->startDate = $program->start_date?->format('Y-m-d');
        $this->endDate = $program->end_date?->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function saveProgram()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'tiers' => $this->tiers,
            'point_rules' => $this->pointRules,
            'rewards_catalog' => $this->rewardsCatalog,
            'is_active' => $this->isActive,
            'start_date' => $this->startDate ? \Carbon\Carbon::parse($this->startDate) : null,
            'end_date' => $this->endDate ? \Carbon\Carbon::parse($this->endDate) : null
        ];

        if ($this->editingProgram) {
            $this->editingProgram->update($data);
            $this->dispatch('program-updated', programId: $this->editingProgram->id);
        } else {
            LoyaltyProgram::create($data);
            $this->dispatch('program-created');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function deleteProgram($programId)
    {
        LoyaltyProgram::findOrFail($programId)->delete();
        $this->dispatch('program-deleted');
        $this->resetPage();
    }

    public function toggleActive($programId)
    {
        $program = LoyaltyProgram::findOrFail($programId);
        $program->update(['is_active' => !$program->is_active]);
        $this->dispatch('program-toggled');
    }

    public function enrollCustomer($contactId, $programId)
    {
        $existingPoints = CustomerLoyaltyPoints::where('contact_id', $contactId)
            ->where('loyalty_program_id', $programId)
            ->first();

        if ($existingPoints) {
            $this->dispatch('customer-already-enrolled');
            return;
        }

        $program = LoyaltyProgram::findOrFail($programId);
        $firstTier = $program->tiers[0] ?? null;

        CustomerLoyaltyPoints::create([
            'contact_id' => $contactId,
            'loyalty_program_id' => $programId,
            'total_points' => 0,
            'available_points' => 0,
            'redeemed_points' => 0,
            'current_tier' => $firstTier ? $firstTier['name'] : 'Bronze',
            'tier_achieved_at' => now(),
            'tier_benefits' => $firstTier ? $firstTier['benefits'] : []
        ]);

        $this->dispatch('customer-enrolled');
    }

    public function awardPoints($customerPointsId, $points, $reason = '')
    {
        $customerPoints = CustomerLoyaltyPoints::findOrFail($customerPointsId);
        $customerPoints->addPoints($points, $reason);
        $this->dispatch('points-awarded');
    }

    public function addTier()
    {
        $this->tiers[] = [
            'name' => '',
            'points_required' => 0,
            'benefits' => [],
            'color' => '#3B82F6'
        ];
    }

    public function removeTier($index)
    {
        unset($this->tiers[$index]);
        $this->tiers = array_values($this->tiers);
    }

    public function addPointRule()
    {
        $this->pointRules['earning'][] = [
            'action' => '',
            'points' => 0,
            'description' => ''
        ];
    }

    public function removePointRule($index)
    {
        unset($this->pointRules['earning'][$index]);
        $this->pointRules['earning'] = array_values($this->pointRules['earning']);
    }

    public function addReward()
    {
        $this->rewardsCatalog[] = [
            'id' => uniqid(),
            'name' => '',
            'description' => '',
            'points_cost' => 0,
            'category' => 'discount',
            'value' => 0,
            'is_available' => true
        ];
    }

    public function removeReward($index)
    {
        unset($this->rewardsCatalog[$index]);
        $this->rewardsCatalog = array_values($this->rewardsCatalog);
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->editingProgram = null;
        $this->reset(['name', 'description', 'tiers', 'pointRules', 'rewardsCatalog', 'isActive', 'startDate', 'endDate']);
    }

    private function initializeDefaults()
    {
        $this->tiers = [
            [
                'name' => 'Bronze',
                'points_required' => 0,
                'benefits' => ['5% discount on purchases'],
                'color' => '#CD7F32'
            ],
            [
                'name' => 'Silver',
                'points_required' => 500,
                'benefits' => ['10% discount on purchases', 'Free shipping'],
                'color' => '#C0C0C0'
            ],
            [
                'name' => 'Gold',
                'points_required' => 1000,
                'benefits' => ['15% discount on purchases', 'Free shipping', 'Priority support'],
                'color' => '#FFD700'
            ]
        ];

        $this->pointRules = [
            'earning' => [
                [
                    'action' => 'purchase',
                    'points' => 1,
                    'description' => '1 point per $1 spent'
                ],
                [
                    'action' => 'review',
                    'points' => 50,
                    'description' => '50 points for product review'
                ],
                [
                    'action' => 'referral',
                    'points' => 100,
                    'description' => '100 points for successful referral'
                ]
            ]
        ];

        $this->rewardsCatalog = [
            [
                'id' => uniqid(),
                'name' => '$5 Off Coupon',
                'description' => '$5 discount on your next purchase',
                'points_cost' => 500,
                'category' => 'discount',
                'value' => 5,
                'is_available' => true
            ],
            [
                'id' => uniqid(),
                'name' => 'Free Shipping',
                'description' => 'Free shipping on your next order',
                'points_cost' => 200,
                'category' => 'shipping',
                'value' => 0,
                'is_available' => true
            ]
        ];
    }
}