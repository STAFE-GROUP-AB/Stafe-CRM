<?php

namespace App\Livewire;

use App\Models\Team;
use App\Services\TeamTheme;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeamThemeSettings extends Component
{
    public ?Team $team = null;
    public string $selectedPreset = 'emerald';

    public function mount(): void
    {
        $this->team = Auth::user()->currentTeam;

        if ($this->team) {
            $settings = $this->team->theme_settings ?? [];
            $this->selectedPreset = $settings['preset'] ?? TeamTheme::DEFAULT_PRESET;
        }
    }

    public function updateTheme(): void
    {
        if (! $this->team) {
            return;
        }

        $this->team->update([
            'theme_settings' => [
                'preset' => $this->selectedPreset,
            ],
        ]);

        $this->dispatch('theme-updated');

        session()->flash('status', 'Theme updated successfully.');
    }

    public function render()
    {
        return view('livewire.team-theme-settings', [
            'presets' => TeamTheme::getPresets(),
        ]);
    }
}
