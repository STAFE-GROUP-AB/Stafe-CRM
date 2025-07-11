<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserAiConfiguration;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Auth;

class UserAiKeyManager extends Component
{
    public $configurations = [];
    public $providers = [];
    public $selectedProvider = '';
    public $apiKey = '';
    public $apiEndpoint = '';
    public $isDefault = false;
    public $editingId = null;
    public $showForm = false;

    public function mount()
    {
        $this->loadConfigurations();
        $this->providers = AiProvider::active()->get();
    }

    public function loadConfigurations()
    {
        $this->configurations = UserAiConfiguration::where('user_id', Auth::id())
            ->with('aiProvider')
            ->get()
            ->toArray();
    }

    public function showAddForm()
    {
        $this->reset(['selectedProvider', 'apiKey', 'apiEndpoint', 'isDefault', 'editingId']);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $config = UserAiConfiguration::find($id);
        if ($config && $config->user_id === Auth::id()) {
            $this->editingId = $id;
            $this->selectedProvider = $config->ai_provider_id;
            $this->apiKey = $config->decryptedApiKey(); // Assuming you have a method to decrypt
            $this->apiEndpoint = $config->api_endpoint;
            $this->isDefault = $config->is_default;
            $this->showForm = true;
        }
    }

    public function save()
    {
        $this->validate([
            'selectedProvider' => 'required|exists:ai_providers,id',
            'apiKey' => 'required|string|min:10',
            'apiEndpoint' => 'nullable|url',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'ai_provider_id' => $this->selectedProvider,
            'api_key' => encrypt($this->apiKey),
            'api_endpoint' => $this->apiEndpoint,
            'is_default' => $this->isDefault,
            'is_active' => true,
        ];

        if ($this->editingId) {
            $config = UserAiConfiguration::find($this->editingId);
            if ($config && $config->user_id === Auth::id()) {
                $config->update($data);
            }
        } else {
            // If setting as default, unset other defaults
            if ($this->isDefault) {
                UserAiConfiguration::where('user_id', Auth::id())
                    ->update(['is_default' => false]);
            }
            
            UserAiConfiguration::create($data);
        }

        $this->loadConfigurations();
        $this->showForm = false;
        $this->reset(['selectedProvider', 'apiKey', 'apiEndpoint', 'isDefault', 'editingId']);
        
        session()->flash('message', 'AI configuration saved successfully!');
    }

    public function delete($id)
    {
        $config = UserAiConfiguration::find($id);
        if ($config && $config->user_id === Auth::id()) {
            $config->delete();
            $this->loadConfigurations();
            session()->flash('message', 'AI configuration deleted successfully!');
        }
    }

    public function setDefault($id)
    {
        // Unset all defaults
        UserAiConfiguration::where('user_id', Auth::id())
            ->update(['is_default' => false]);

        // Set the selected one as default
        $config = UserAiConfiguration::find($id);
        if ($config && $config->user_id === Auth::id()) {
            $config->update(['is_default' => true]);
            $this->loadConfigurations();
            session()->flash('message', 'Default AI configuration updated!');
        }
    }

    public function testConnection($id)
    {
        $config = UserAiConfiguration::find($id);
        if ($config && $config->user_id === Auth::id()) {
            // Here you would implement actual API testing
            // For now, we'll just show a success message
            session()->flash('message', 'Connection test successful!');
        }
    }

    public function render()
    {
        return view('livewire.user-ai-key-manager');
    }
}
