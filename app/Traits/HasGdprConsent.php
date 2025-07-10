<?php

namespace App\Traits;

use App\Models\GdprConsent;
use App\Services\Security\GdprComplianceService;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasGdprConsent
{
    public function gdprConsents(): MorphMany
    {
        return $this->morphMany(GdprConsent::class, 'consentable');
    }

    public function hasValidConsent(string $purpose): bool
    {
        $service = app(GdprComplianceService::class);
        return $service->hasValidConsent($this, $purpose);
    }

    public function grantConsent(
        string $purpose, 
        string $legalBasis = 'consent',
        ?int $expiresInDays = null,
        array $metadata = []
    ): GdprConsent {
        $service = app(GdprComplianceService::class);
        return $service->recordConsent($this, $purpose, $legalBasis, $expiresInDays, $metadata);
    }

    public function withdrawConsent(string $purpose): bool
    {
        $service = app(GdprComplianceService::class);
        return $service->withdrawConsent($this, $purpose);
    }

    public function getGdprExportableRelations(): array
    {
        // Override this method in your models to specify which relations
        // should be included in GDPR data exports
        return [];
    }

    public function getGdprAnonymizableFields(): array
    {
        // Override this method to specify which fields should be anonymized
        // when GDPR erasure is requested
        return ['email', 'phone', 'first_name', 'last_name'];
    }

    public function exportGdprData(): array
    {
        $service = app(GdprComplianceService::class);
        return $service->exportPersonalData($this);
    }

    public function anonymizeGdprData(): bool
    {
        $service = app(GdprComplianceService::class);
        return $service->anonymizeData($this);
    }

    public function canProcessPersonalData(string $purpose): bool
    {
        // Check if we have valid consent or another legal basis for processing
        return $this->hasValidConsent($purpose) || 
               $this->hasLegalBasisForProcessing($purpose);
    }

    protected function hasLegalBasisForProcessing(string $purpose): bool
    {
        // This could be customized based on your business logic
        // For example, contract performance, legal obligation, etc.
        
        $legitimateBases = [
            'essential' => true, // Essential service functionality
            'contract' => $this->hasActiveContract(),
            'legal_obligation' => true, // When required by law
        ];

        return $legitimateBases[$purpose] ?? false;
    }

    protected function hasActiveContract(): bool
    {
        // Override this method to implement your contract checking logic
        // For example, check if the customer has an active subscription
        return false;
    }
}