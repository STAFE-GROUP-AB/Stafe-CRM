<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataEnrichmentService
{
    /**
     * Enrich contact data using multiple providers
     */
    public function enrichContact(Contact $contact, array $providers = ['clearbit', 'hunter']): array
    {
        $enrichedData = [];
        $sources = [];

        foreach ($providers as $provider) {
            try {
                $data = $this->enrichWithProvider($contact, $provider);
                
                if ($data) {
                    $enrichedData = array_merge($enrichedData, $data);
                    $sources[] = $provider;
                }
            } catch (\Exception $e) {
                Log::warning("Enrichment failed for provider: {$provider}", [
                    'contact_id' => $contact->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($enrichedData)) {
            $this->applyEnrichedData($contact, $enrichedData, $sources);
        }

        return [
            'enriched' => !empty($enrichedData),
            'sources' => $sources,
            'data' => $enrichedData,
        ];
    }

    /**
     * Enrich company data
     */
    public function enrichCompany(Company $company, array $providers = ['clearbit', 'crunchbase']): array
    {
        $enrichedData = [];
        $sources = [];

        foreach ($providers as $provider) {
            try {
                $data = $this->enrichCompanyWithProvider($company, $provider);
                
                if ($data) {
                    $enrichedData = array_merge($enrichedData, $data);
                    $sources[] = $provider;
                }
            } catch (\Exception $e) {
                Log::warning("Company enrichment failed for provider: {$provider}", [
                    'company_id' => $company->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($enrichedData)) {
            $this->applyEnrichedCompanyData($company, $enrichedData, $sources);
        }

        return [
            'enriched' => !empty($enrichedData),
            'sources' => $sources,
            'data' => $enrichedData,
        ];
    }

    /**
     * Enrich contact with specific provider
     */
    protected function enrichWithProvider(Contact $contact, string $provider): ?array
    {
        return match ($provider) {
            'clearbit' => $this->enrichWithClearbit($contact),
            'hunter' => $this->enrichWithHunter($contact),
            'fullcontact' => $this->enrichWithFullContact($contact),
            'pipl' => $this->enrichWithPipl($contact),
            default => null,
        };
    }

    /**
     * Enrich using Clearbit Enrichment API
     */
    protected function enrichWithClearbit(Contact $contact): ?array
    {
        $apiKey = config('services.clearbit.api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($apiKey, '')
                ->get('https://person.clearbit.com/v2/combined/find', [
                    'email' => $contact->email,
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $person = $data['person'] ?? [];
            $company = $data['company'] ?? [];

            return [
                'first_name' => $person['name']['givenName'] ?? null,
                'last_name' => $person['name']['familyName'] ?? null,
                'title' => $person['employment']['title'] ?? null,
                'bio' => $person['bio'] ?? null,
                'linkedin_url' => $person['linkedin']['handle'] ?? null,
                'twitter_url' => $person['twitter']['handle'] ?? null,
                'facebook_url' => $person['facebook']['handle'] ?? null,
                'city' => $person['geo']['city'] ?? null,
                'state' => $person['geo']['state'] ?? null,
                'country' => $person['geo']['country'] ?? null,
                'timezone' => $person['timeZone'] ?? null,
                'company_name' => $company['name'] ?? null,
                'company_domain' => $company['domain'] ?? null,
                'company_industry' => $company['category']['industry'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Clearbit enrichment failed', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Enrich using Hunter.io
     */
    protected function enrichWithHunter(Contact $contact): ?array
    {
        $apiKey = config('services.hunter.api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::get('https://api.hunter.io/v2/email-verifier', [
                'email' => $contact->email,
                'api_key' => $apiKey,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json()['data'] ?? [];

            return [
                'email_verified' => $data['status'] === 'valid',
                'email_score' => $data['score'] ?? null,
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'company_name' => $data['organization'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Hunter enrichment failed', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Enrich using FullContact API
     */
    protected function enrichWithFullContact(Contact $contact): ?array
    {
        $apiKey = config('services.fullcontact.api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.fullcontact.com/v3/person.enrich', [
                'email' => $contact->email,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'full_name' => $data['fullName'] ?? null,
                'title' => $data['title'] ?? null,
                'bio' => $data['bio'] ?? null,
                'linkedin_url' => $data['linkedin'] ?? null,
                'twitter_url' => $data['twitter'] ?? null,
                'city' => $data['location']['city'] ?? null,
                'country' => $data['location']['country'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('FullContact enrichment failed', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Enrich using Pipl API
     */
    protected function enrichWithPipl(Contact $contact): ?array
    {
        $apiKey = config('services.pipl.api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::get('https://api.pipl.com/search/', [
                'email' => $contact->email,
                'key' => $apiKey,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $person = $data['person'] ?? [];

            return [
                'full_name' => $person['names'][0]['display'] ?? null,
                'phone' => $person['phones'][0]['display'] ?? null,
                'city' => $person['addresses'][0]['city'] ?? null,
                'country' => $person['addresses'][0]['country'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Pipl enrichment failed', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Enrich company with provider
     */
    protected function enrichCompanyWithProvider(Company $company, string $provider): ?array
    {
        return match ($provider) {
            'clearbit' => $this->enrichCompanyWithClearbit($company),
            'crunchbase' => $this->enrichCompanyWithCrunchbase($company),
            default => null,
        };
    }

    /**
     * Enrich company using Clearbit Company API
     */
    protected function enrichCompanyWithClearbit(Company $company): ?array
    {
        $apiKey = config('services.clearbit.api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $domain = $company->website ? parse_url($company->website, PHP_URL_HOST) : null;
            
            if (!$domain) {
                return null;
            }

            $response = Http::withBasicAuth($apiKey, '')
                ->get('https://company.clearbit.com/v2/companies/find', [
                    'domain' => $domain,
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'industry' => $data['category']['industry'] ?? null,
                'employee_count' => $data['metrics']['employees'] ?? null,
                'annual_revenue' => $data['metrics']['estimatedAnnualRevenue'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['geo']['streetAddress'] ?? null,
                'city' => $data['geo']['city'] ?? null,
                'state' => $data['geo']['state'] ?? null,
                'country' => $data['geo']['country'] ?? null,
                'postal_code' => $data['geo']['postalCode'] ?? null,
                'linkedin_url' => $data['linkedin']['handle'] ?? null,
                'twitter_url' => $data['twitter']['handle'] ?? null,
                'facebook_url' => $data['facebook']['handle'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Clearbit company enrichment failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Enrich company using Crunchbase API
     */
    protected function enrichCompanyWithCrunchbase(Company $company): ?array
    {
        $apiKey = config('services.crunchbase.api_key');
        
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-cb-user-key' => $apiKey,
            ])->get('https://api.crunchbase.com/api/v4/autocompletes', [
                'query' => $company->name,
                'collection_ids' => 'organizations',
            ]);

            if (!$response->successful() || empty($response->json()['entities'])) {
                return null;
            }

            $orgId = $response->json()['entities'][0]['identifier']['uuid'] ?? null;
            
            if (!$orgId) {
                return null;
            }

            // Get full organization data
            $orgResponse = Http::withHeaders([
                'X-cb-user-key' => $apiKey,
            ])->get("https://api.crunchbase.com/api/v4/entities/organizations/{$orgId}");

            if (!$orgResponse->successful()) {
                return null;
            }

            $data = $orgResponse->json()['properties'] ?? [];

            return [
                'description' => $data['short_description'] ?? null,
                'employee_count' => $data['num_employees_enum'] ?? null,
                'annual_revenue' => $data['revenue_range'] ?? null,
                'founded_year' => $data['founded_on']['value'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Crunchbase enrichment failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Apply enriched data to contact
     */
    protected function applyEnrichedData(Contact $contact, array $enrichedData, array $sources): void
    {
        $updates = [];

        foreach ($enrichedData as $field => $value) {
            // Only update if field is empty and value is not null
            if (empty($contact->$field) && !empty($value)) {
                $updates[$field] = $value;
            }
        }

        if (!empty($updates)) {
            $contact->update($updates);
            
            // Track enrichment metadata
            $metadata = $contact->metadata ?? [];
            $metadata['enrichment'] = [
                'sources' => $sources,
                'enriched_at' => now()->toISOString(),
                'fields_updated' => array_keys($updates),
            ];
            
            $contact->update(['metadata' => $metadata]);

            Log::info('Contact enriched', [
                'contact_id' => $contact->id,
                'sources' => $sources,
                'fields_updated' => count($updates),
            ]);
        }
    }

    /**
     * Apply enriched data to company
     */
    protected function applyEnrichedCompanyData(Company $company, array $enrichedData, array $sources): void
    {
        $updates = [];

        foreach ($enrichedData as $field => $value) {
            if (empty($company->$field) && !empty($value)) {
                $updates[$field] = $value;
            }
        }

        if (!empty($updates)) {
            $company->update($updates);
            
            Log::info('Company enriched', [
                'company_id' => $company->id,
                'sources' => $sources,
                'fields_updated' => count($updates),
            ]);
        }
    }

    /**
     * Batch enrich multiple contacts
     */
    public function batchEnrichContacts(array $contactIds, array $providers = ['clearbit']): array
    {
        $results = [
            'total' => count($contactIds),
            'enriched' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($contactIds as $contactId) {
            $contact = Contact::find($contactId);
            
            if (!$contact || !$contact->email) {
                $results['skipped']++;
                continue;
            }

            $result = $this->enrichContact($contact, $providers);
            
            if ($result['enriched']) {
                $results['enriched']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Get enrichment quality score
     */
    public function getEnrichmentQuality(Contact $contact): array
    {
        $fields = [
            'first_name', 'last_name', 'email', 'phone', 'mobile',
            'title', 'company_id', 'city', 'country', 'linkedin_url'
        ];

        $filledFields = 0;
        $totalFields = count($fields);

        foreach ($fields as $field) {
            if (!empty($contact->$field)) {
                $filledFields++;
            }
        }

        $completeness = ($filledFields / $totalFields) * 100;

        return [
            'completeness_percentage' => round($completeness, 2),
            'filled_fields' => $filledFields,
            'total_fields' => $totalFields,
            'missing_fields' => array_filter($fields, fn($f) => empty($contact->$f)),
            'quality_grade' => match (true) {
                $completeness >= 90 => 'A',
                $completeness >= 75 => 'B',
                $completeness >= 50 => 'C',
                $completeness >= 25 => 'D',
                default => 'F',
            },
        ];
    }
}
