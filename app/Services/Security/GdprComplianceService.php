<?php

namespace App\Services\Security;

use App\Models\GdprConsent;
use App\Models\GdprDataRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GdprComplianceService
{
    public function recordConsent(
        Model $subject,
        string $purpose,
        string $legalBasis,
        ?int $expiresInDays = null,
        array $metadata = []
    ): GdprConsent {
        return GdprConsent::create([
            'tenant_id' => $subject->tenant_id ?? auth()->user()?->tenant_id,
            'consentable_type' => get_class($subject),
            'consentable_id' => $subject->id,
            'purpose' => $purpose,
            'status' => 'granted',
            'legal_basis' => $legalBasis,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'granted_at' => now(),
            'expires_at' => $expiresInDays ? now()->addDays($expiresInDays) : null,
            'metadata' => $metadata,
        ]);
    }

    public function withdrawConsent(Model $subject, string $purpose): bool
    {
        $consents = GdprConsent::where('consentable_type', get_class($subject))
            ->where('consentable_id', $subject->id)
            ->where('purpose', $purpose)
            ->where('status', 'granted')
            ->get();

        foreach ($consents as $consent) {
            $consent->withdraw();
        }

        return $consents->isNotEmpty();
    }

    public function hasValidConsent(Model $subject, string $purpose): bool
    {
        return GdprConsent::where('consentable_type', get_class($subject))
            ->where('consentable_id', $subject->id)
            ->forPurpose($purpose)
            ->active()
            ->exists();
    }

    public function getConsentHistory(Model $subject): Collection
    {
        return GdprConsent::where('consentable_type', get_class($subject))
            ->where('consentable_id', $subject->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createDataRequest(
        string $type,
        string $requesterEmail,
        Model $subject,
        ?string $description = null
    ): GdprDataRequest {
        $deadlineDays = match ($type) {
            'access', 'portability' => 30,
            'rectification', 'erasure', 'restriction' => 30,
            default => 30,
        };

        return GdprDataRequest::create([
            'tenant_id' => $subject->tenant_id ?? auth()->user()?->tenant_id,
            'requestable_type' => get_class($subject),
            'requestable_id' => $subject->id,
            'type' => $type,
            'requester_email' => $requesterEmail,
            'description' => $description,
            'deadline' => now()->addDays($deadlineDays),
        ]);
    }

    public function exportPersonalData(Model $subject): array
    {
        $data = [
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'exported_at' => now()->toISOString(),
            'data' => $subject->toArray(),
        ];

        // Include related data based on model type
        if (method_exists($subject, 'getGdprExportableRelations')) {
            $relations = $subject->getGdprExportableRelations();
            foreach ($relations as $relation) {
                $data['related'][$relation] = $subject->$relation()->get()->toArray();
            }
        }

        // Include consent history
        $data['consents'] = $this->getConsentHistory($subject)->toArray();

        return $data;
    }

    public function anonymizeData(Model $subject): bool
    {
        // Get fields that should be anonymized
        $anonymizableFields = method_exists($subject, 'getGdprAnonymizableFields') 
            ? $subject->getGdprAnonymizableFields() 
            : ['email', 'phone', 'first_name', 'last_name'];

        $updates = [];
        foreach ($anonymizableFields as $field) {
            if ($subject->getAttribute($field)) {
                $updates[$field] = $this->generateAnonymizedValue($field);
            }
        }

        if (!empty($updates)) {
            $subject->update($updates);
            return true;
        }

        return false;
    }

    public function deletePersonalData(Model $subject): bool
    {
        // First withdraw all consents
        GdprConsent::where('consentable_type', get_class($subject))
            ->where('consentable_id', $subject->id)
            ->update(['status' => 'withdrawn', 'withdrawn_at' => now()]);

        // Then delete the subject
        return $subject->delete();
    }

    protected function generateAnonymizedValue(string $field): string
    {
        return match ($field) {
            'email' => 'anonymized_' . uniqid() . '@example.com',
            'phone' => '***-***-' . rand(1000, 9999),
            'first_name', 'last_name' => 'Anonymized',
            default => 'ANONYMIZED',
        };
    }

    public function getPendingRequests(): Collection
    {
        return GdprDataRequest::pending()
            ->orderBy('deadline')
            ->get();
    }

    public function getOverdueRequests(): Collection
    {
        return GdprDataRequest::overdue()
            ->orderBy('deadline')
            ->get();
    }
}