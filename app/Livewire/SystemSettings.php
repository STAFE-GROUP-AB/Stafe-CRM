<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SystemSetting;
use Livewire\WithFileUploads;

class SystemSettings extends Component
{
    use WithFileUploads;

    public $app_name;
    public $app_logo;
    public $primary_color;
    public $secondary_color;
    public $logo_upload;

    protected $rules = [
        'app_name' => 'required|string|max:255',
        'primary_color' => 'required|string|max:7',
        'secondary_color' => 'required|string|max:7',
        'logo_upload' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->app_name = SystemSetting::get('app_name', 'Stafe CRM');
        $this->app_logo = SystemSetting::get('app_logo');
        $this->primary_color = SystemSetting::get('primary_color', '#3B82F6');
        $this->secondary_color = SystemSetting::get('secondary_color', '#6B7280');
    }

    public function save()
    {
        $this->validate();

        SystemSetting::set('app_name', $this->app_name, 'Application name displayed in the header');
        SystemSetting::set('primary_color', $this->primary_color, 'Primary theme color', 'color');
        SystemSetting::set('secondary_color', $this->secondary_color, 'Secondary theme color', 'color');

        if ($this->logo_upload) {
            $path = $this->logo_upload->store('logos', 'public');
            SystemSetting::set('app_logo', '/storage/' . $path, 'Application logo URL');
        }

        session()->flash('message', 'Settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.system-settings')->layout('layouts.app');
    }
}