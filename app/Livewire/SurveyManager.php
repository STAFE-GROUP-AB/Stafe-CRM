<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Survey;
use App\Models\SurveyResponse;

class SurveyManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $editingSurvey = null;
    public $name = '';
    public $description = '';
    public $type = 'custom';
    public $questions = [];
    public $isActive = true;
    public $startDate = '';
    public $endDate = '';

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'type' => 'required|in:nps,csat,ces,custom',
        'questions' => 'required|array|min:1',
        'isActive' => 'boolean',
        'startDate' => 'nullable|date',
        'endDate' => 'nullable|date|after:start_date'
    ];

    public function render()
    {
        $surveys = Survey::with('creator')
            ->withCount(['responses', 'completedResponses'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_surveys' => Survey::count(),
            'active_surveys' => Survey::active()->count(),
            'total_responses' => SurveyResponse::count(),
            'completion_rate' => $this->calculateOverallCompletionRate()
        ];

        return view('livewire.survey-manager', compact('surveys', 'stats'));
    }

    public function createSurvey()
    {
        $this->showCreateModal = true;
        $this->reset(['name', 'description', 'type', 'questions', 'isActive', 'startDate', 'endDate']);
        $this->addDefaultQuestions();
    }

    public function editSurvey($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        $this->editingSurvey = $survey;
        $this->name = $survey->name;
        $this->description = $survey->description;
        $this->type = $survey->type;
        $this->questions = $survey->questions;
        $this->isActive = $survey->is_active;
        $this->startDate = $survey->start_date?->format('Y-m-d');
        $this->endDate = $survey->end_date?->format('Y-m-d');
        $this->showCreateModal = true;
    }

    public function saveSurvey()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'questions' => $this->questions,
            'is_active' => $this->isActive,
            'start_date' => $this->startDate ? \Carbon\Carbon::parse($this->startDate) : null,
            'end_date' => $this->endDate ? \Carbon\Carbon::parse($this->endDate) : null,
            'created_by' => auth()->id()
        ];

        if ($this->editingSurvey) {
            $this->editingSurvey->update($data);
            $this->dispatch('survey-updated', surveyId: $this->editingSurvey->id);
        } else {
            Survey::create($data);
            $this->dispatch('survey-created');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function deleteSurvey($surveyId)
    {
        Survey::findOrFail($surveyId)->delete();
        $this->dispatch('survey-deleted');
        $this->resetPage();
    }

    public function toggleActive($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        $survey->update(['is_active' => !$survey->is_active]);
        $this->dispatch('survey-toggled');
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'text' => '',
            'type' => 'text',
            'required' => true,
            'options' => []
        ];
    }

    public function removeQuestion($index)
    {
        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function addOption($questionIndex)
    {
        if (!isset($this->questions[$questionIndex]['options'])) {
            $this->questions[$questionIndex]['options'] = [];
        }
        $this->questions[$questionIndex]['options'][] = '';
    }

    public function removeOption($questionIndex, $optionIndex)
    {
        unset($this->questions[$questionIndex]['options'][$optionIndex]);
        $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->editingSurvey = null;
        $this->reset(['name', 'description', 'type', 'questions', 'isActive', 'startDate', 'endDate']);
    }

    public function updatedType()
    {
        $this->addDefaultQuestions();
    }

    private function addDefaultQuestions()
    {
        switch ($this->type) {
            case 'nps':
                $this->questions = [
                    [
                        'text' => 'How likely are you to recommend our product/service to others?',
                        'type' => 'rating',
                        'required' => true,
                        'scale' => [0, 10],
                        'labels' => ['Not likely at all', 'Extremely likely']
                    ],
                    [
                        'text' => 'What is the primary reason for your score?',
                        'type' => 'textarea',
                        'required' => false
                    ]
                ];
                break;
            case 'csat':
                $this->questions = [
                    [
                        'text' => 'How satisfied are you with our product/service?',
                        'type' => 'rating',
                        'required' => true,
                        'scale' => [1, 5],
                        'labels' => ['Very dissatisfied', 'Very satisfied']
                    ],
                    [
                        'text' => 'Any additional feedback?',
                        'type' => 'textarea',
                        'required' => false
                    ]
                ];
                break;
            case 'ces':
                $this->questions = [
                    [
                        'text' => 'How easy was it to resolve your issue?',
                        'type' => 'rating',
                        'required' => true,
                        'scale' => [1, 7],
                        'labels' => ['Very difficult', 'Very easy']
                    ]
                ];
                break;
            default:
                $this->questions = [
                    [
                        'text' => 'Sample question',
                        'type' => 'text',
                        'required' => true,
                        'options' => []
                    ]
                ];
        }
    }

    private function calculateOverallCompletionRate(): float
    {
        $totalResponses = SurveyResponse::count();
        $completedResponses = SurveyResponse::where('is_completed', true)->count();
        
        return $totalResponses > 0 ? round(($completedResponses / $totalResponses) * 100, 1) : 0;
    }
}