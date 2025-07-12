<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasGdprConsent;
use App\Traits\HasEncryptedFields;
use App\Traits\HasAuditLog;

class Contact extends Model
{
    use HasFactory, HasGdprConsent, HasEncryptedFields, HasAuditLog;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'title',
        'department',
        'company_id',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'timezone',
        'birthday',
        'bio',
        'social_links',
        'custom_fields',
        'avatar_url',
        'status',
        'source',
        'lifetime_value',
        'last_contacted_at',
        'owner_id',
    ];

    protected $casts = [
        'birthday' => 'date',
        'social_links' => 'array',
        'custom_fields' => 'array',
        'lifetime_value' => 'decimal:2',
        'last_contacted_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'emailable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function leadScore(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LeadScore::class);
    }

    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    public function getFormattedValueAttribute(): string
    {
        if (!$this->lifetime_value) {
            return '';
        }
        
        return number_format($this->lifetime_value, 2) . ' USD';
    }

    // GDPR-specific methods
    public function getGdprExportableRelations(): array
    {
        return [
            'deals',
            'tasks',
            'notes',
            'customFields',
            'emails',
            'communications',
        ];
    }

    public function getGdprAnonymizableFields(): array
    {
        return [
            'first_name',
            'last_name', 
            'email',
            'phone',
            'personal_email',
            'mobile_phone',
            'linkedin_url',
            'bio',
            'birthday',
        ];
    }

    protected function getSensitiveAuditFields(): array
    {
        return [
            'ssn',
            'tax_id',
            'personal_email', // If different from business email
            'personal_phone',
        ];
    }

    /**
     * Calculate and return the health score for this contact
     */
    public function healthScore(): float
    {
        // If there's a customer health score record, use it
        if ($this->customerHealthScore) {
            return $this->customerHealthScore->score;
        }
        
        // Otherwise, calculate a basic health score based on engagement
        $score = 50; // Start with neutral score
        
        // Positive factors
        if ($this->last_contacted_at && $this->last_contacted_at->diffInDays() < 30) {
            $score += 20; // Recent contact
        }
        
        if ($this->deals()->where('status', 'open')->exists()) {
            $score += 15; // Active deals
        }
        
        if ($this->communications()->where('created_at', '>', now()->subDays(30))->exists()) {
            $score += 10; // Recent communications
        }
        
        if ($this->lifetime_value && $this->lifetime_value > 0) {
            $score += 10; // Has value
        }
        
        // Negative factors
        if ($this->last_contacted_at && $this->last_contacted_at->diffInDays() > 90) {
            $score -= 15; // No recent contact
        }
        
        if (!$this->last_contacted_at) {
            $score -= 10; // Never contacted
        }
        
        return max(0, min(100, $score));
    }

    /**
     * Get the customer health score relationship
     */
    public function customerHealthScore(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CustomerHealthScore::class);
    }
}
