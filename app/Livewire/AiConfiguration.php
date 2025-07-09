<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\AiService;
use App\Models\AiProvider;
use App\Models\UserAiConfiguration;
use Illuminate\Support\Facades\Auth;

class AiConfiguration extends Component
{
    public $providers = [];
    public $selectedProvider = null;
    public $availableModels = [];
    public $userConfigurations = [];
    
    // Form fields
    public $configurationName = '';
    public $apiKey = '';
    public $organization = '';
    public $defaultModels = [];
    public $isDefault = false;
    public $showAddForm = false;
    public $editingConfig = null;
    public $testResult = null;

    protected $rules = [
        'configurationName' => 'required|string|max:255',
        'apiKey' => 'required|string',
        'selectedProvider' => 'required|exists:ai_providers,id',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $aiService = new AiService();
        $this->providers = $aiService->getAvailableProviders();
        $this->userConfigurations = $aiService->getUserConfigurations(Auth::id());
    }

    public function selectProvider($providerId)
    {
        $this->selectedProvider = $providerId;
        $aiService = new AiService();
        $this->availableModels = $aiService->getProviderModels($providerId);
        
        // Reset form when provider changes
        $this->resetForm();
    }

    public function showAddConfigurationForm()
    {
        $this->showAddForm = true;
        $this->resetForm();
    }

    public function cancelForm()
    {
        $this->showAddForm = false;
        $this->editingConfig = null;
        $this->resetForm();
    }

    public function saveConfiguration()
    {
        $this->validate();

        $credentials = ['api_key' => $this->apiKey];
        if ($this->organization) {
            $credentials['organization'] = $this->organization;
        }

        $data = [
            'ai_provider_id' => $this->selectedProvider,
            'name' => $this->configurationName,
            'credentials' => $credentials,
            'default_models' => $this->defaultModels,
            'is_default' => $this->isDefault,
            'is_active' => true,
        ];

        $aiService = new AiService();
        
        if ($this->editingConfig) {
            // Update existing configuration
            $config = UserAiConfiguration::find($this->editingConfig);
            $config->update($data);
        } else {
            // Create new configuration
            $aiService->configureUserAi(Auth::id(), $data);
        }

        $this->loadData();
        $this->cancelForm();
        
        session()->flash('success', $this->editingConfig ? 'Configuration updated!' : 'Configuration saved!');
    }

    public function editConfiguration($configId)
    {
        $config = UserAiConfiguration::find($configId);
        
        $this->editingConfig = $configId;
        $this->selectedProvider = $config->ai_provider_id;
        $this->configurationName = $config->name;
        $this->apiKey = $config->getCredential('api_key') ?? '';
        $this->organization = $config->getCredential('organization') ?? '';
        $this->defaultModels = $config->default_models ?? [];
        $this->isDefault = $config->is_default;
        $this->showAddForm = true;
        
        $this->selectProvider($this->selectedProvider);
    }

    public function deleteConfiguration($configId)
    {
        UserAiConfiguration::find($configId)->delete();
        $this->loadData();
        session()->flash('success', 'Configuration deleted!');
    }

    public function testConfiguration($configId)
    {
        $config = UserAiConfiguration::find($configId);
        $aiService = new AiService();
        
        $this->testResult = $aiService->testConfiguration($config);
        
        if ($this->testResult['success']) {
            session()->flash('success', 'Configuration test successful!');
        } else {
            session()->flash('error', $this->testResult['message']);
        }
    }

    public function setDefaultConfiguration($configId)
    {
        // Remove default from all user's configurations
        UserAiConfiguration::forUser(Auth::id())->update(['is_default' => false]);
        
        // Set this one as default
        UserAiConfiguration::find($configId)->update(['is_default' => true]);
        
        $this->loadData();
        session()->flash('success', 'Default configuration updated!');
    }

    private function resetForm()
    {
        $this->configurationName = '';
        $this->apiKey = '';
        $this->organization = '';
        $this->defaultModels = [];
        $this->isDefault = false;
        $this->testResult = null;
    }

    public function render()
    {
        return view('livewire.ai-configuration');
    }
}
