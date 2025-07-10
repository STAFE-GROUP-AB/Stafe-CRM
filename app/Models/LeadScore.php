<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'score',
        'probability',
        'grade',
        'factors',
        'explanations',
        'model_version',
        'last_calculated_at',
        'ai_model_id',
        'raw_predictions',
    ];

    protected $casts = [
        'factors' => 'array',
        'explanations' => 'array',
        'probability' => 'decimal:4',
        'raw_predictions' => 'array',
        'last_calculated_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function scopeHighScore($query, $threshold = 70)
    {
        return $query->where('score', '>=', $threshold);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    public function getScoreColor(): string
    {
        return match (true) {
            $this->score >= 80 => 'green',
            $this->score >= 60 => 'yellow',
            $this->score >= 40 => 'orange',
            default => 'red'
        };
    }

    public function getGradeFromScore(): string
    {
        return match (true) {
            $this->score >= 90 => 'A',
            $this->score >= 80 => 'B',
            $this->score >= 70 => 'C',
            $this->score >= 60 => 'D',
            default => 'F'
        };
    }

    public function isStale(int $hoursThreshold = 24): bool
    {
        return $this->last_calculated_at->diffInHours(now()) > $hoursThreshold;
    }
}
