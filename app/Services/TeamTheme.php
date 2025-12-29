<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class TeamTheme
{
    public const COLOR_PRESETS = [
        'stafe' => [
            'name' => 'Stafe (Default)',
            'primary' => 'emerald',
            'primary_hover' => 'emerald',
            'accent' => 'yellow',
            'description' => 'Emerald buttons on warm amber background',
        ],
        'emerald' => [
            'name' => 'Emerald',
            'primary' => 'emerald',
            'primary_hover' => 'emerald',
            'accent' => 'teal',
        ],
        'blue' => [
            'name' => 'Blue',
            'primary' => 'blue',
            'primary_hover' => 'blue',
            'accent' => 'sky',
        ],
        'indigo' => [
            'name' => 'Indigo',
            'primary' => 'indigo',
            'primary_hover' => 'indigo',
            'accent' => 'violet',
        ],
        'violet' => [
            'name' => 'Violet',
            'primary' => 'violet',
            'primary_hover' => 'violet',
            'accent' => 'purple',
        ],
        'rose' => [
            'name' => 'Rose',
            'primary' => 'rose',
            'primary_hover' => 'rose',
            'accent' => 'pink',
        ],
        'amber' => [
            'name' => 'Amber',
            'primary' => 'amber',
            'primary_hover' => 'amber',
            'accent' => 'orange',
        ],
        'cyan' => [
            'name' => 'Cyan',
            'primary' => 'cyan',
            'primary_hover' => 'cyan',
            'accent' => 'sky',
        ],
        'lime' => [
            'name' => 'Lime',
            'primary' => 'lime',
            'primary_hover' => 'lime',
            'accent' => 'green',
        ],
    ];

    public const DEFAULT_PRESET = 'stafe';

    protected ?Team $team;
    protected array $preset;

    public function __construct(?Team $team = null)
    {
        $this->team = $team ?? Auth::user()?->currentTeam;
        $this->preset = $this->resolvePreset();
    }

    protected function resolvePreset(): array
    {
        $presetKey = self::DEFAULT_PRESET;

        if ($this->team) {
            $settings = $this->team->theme_settings ?? [];
            $presetKey = $settings['preset'] ?? self::DEFAULT_PRESET;
        }

        return self::COLOR_PRESETS[$presetKey] ?? self::COLOR_PRESETS[self::DEFAULT_PRESET];
    }

    public function primary(): string
    {
        return $this->preset['primary'];
    }

    public function accent(): string
    {
        return $this->preset['accent'];
    }

    public function presetKey(): string
    {
        if ($this->team) {
            $settings = $this->team->theme_settings ?? [];
            return $settings['preset'] ?? self::DEFAULT_PRESET;
        }
        return self::DEFAULT_PRESET;
    }

    /**
     * Get button classes for primary buttons
     */
    public function buttonPrimary(): string
    {
        $color = $this->primary();
        return "bg-{$color}-600 hover:bg-{$color}-700 focus:ring-{$color}-500 dark:bg-{$color}-500 dark:hover:bg-{$color}-600";
    }

    /**
     * Get link/text classes for primary colored text
     */
    public function textPrimary(): string
    {
        $color = $this->primary();
        return "text-{$color}-600 hover:text-{$color}-900 dark:text-{$color}-400 dark:hover:text-{$color}-300";
    }

    /**
     * Get focus ring classes
     */
    public function focusRing(): string
    {
        $color = $this->primary();
        return "focus:ring-{$color}-500 focus:border-{$color}-500";
    }

    /**
     * Get badge classes for primary colored badges
     */
    public function badgePrimary(): string
    {
        $color = $this->primary();
        return "bg-{$color}-100 text-{$color}-800 dark:bg-{$color}-900 dark:text-{$color}-300";
    }

    /**
     * Get all available presets for settings UI
     */
    public static function getPresets(): array
    {
        return self::COLOR_PRESETS;
    }

    /**
     * Static helper to get theme for current user's team
     */
    public static function current(): self
    {
        return new self();
    }
}
