<?php

namespace App\Services;

use App\Models\DynamicContentTemplate;
use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use Illuminate\Support\Str;

class DynamicContentService
{
    /**
     * Render dynamic content with personalization
     */
    public function render(DynamicContentTemplate $template, $entity, array $additionalData = []): string
    {
        $content = $template->content;
        
        // Get personalization variables
        $variables = $this->extractVariables($entity, $additionalData);
        
        // Apply personalization rules
        $content = $this->applyPersonalizationRules($content, $template->personalization_rules ?? [], $entity);
        
        // Replace variables
        $content = $this->replaceVariables($content, $variables);
        
        // Apply conditional content
        $content = $this->applyConditionalContent($content, $variables);
        
        return $content;
    }

    /**
     * Extract variables from entity and additional data
     */
    protected function extractVariables($entity, array $additionalData): array
    {
        $variables = $additionalData;
        
        // Extract variables based on entity type
        if ($entity instanceof Contact) {
            $variables = array_merge($variables, $this->getContactVariables($entity));
        } elseif ($entity instanceof Company) {
            $variables = array_merge($variables, $this->getCompanyVariables($entity));
        } elseif ($entity instanceof Deal) {
            $variables = array_merge($variables, $this->getDealVariables($entity));
        }
        
        // Add system variables
        $variables = array_merge($variables, $this->getSystemVariables());
        
        return $variables;
    }

    /**
     * Get contact-specific variables
     */
    protected function getContactVariables(Contact $contact): array
    {
        return [
            'contact.first_name' => $contact->first_name,
            'contact.last_name' => $contact->last_name,
            'contact.full_name' => $contact->first_name . ' ' . $contact->last_name,
            'contact.email' => $contact->email,
            'contact.phone' => $contact->phone,
            'contact.title' => $contact->title,
            'contact.company' => $contact->company?->name,
            'contact.city' => $contact->city,
            'contact.country' => $contact->country,
        ];
    }

    /**
     * Get company-specific variables
     */
    protected function getCompanyVariables(Company $company): array
    {
        return [
            'company.name' => $company->name,
            'company.industry' => $company->industry,
            'company.website' => $company->website,
            'company.phone' => $company->phone,
            'company.city' => $company->city,
            'company.country' => $company->country,
            'company.employee_count' => $company->employee_count,
        ];
    }

    /**
     * Get deal-specific variables
     */
    protected function getDealVariables(Deal $deal): array
    {
        return [
            'deal.name' => $deal->name,
            'deal.value' => number_format($deal->value, 2),
            'deal.stage' => $deal->pipelineStage?->name,
            'deal.probability' => $deal->probability,
            'deal.expected_close_date' => $deal->expected_close_date?->format('Y-m-d'),
            'deal.company' => $deal->company?->name,
            'deal.contact' => $deal->contact ? $deal->contact->first_name . ' ' . $deal->contact->last_name : null,
        ];
    }

    /**
     * Get system variables
     */
    protected function getSystemVariables(): array
    {
        return [
            'system.date' => now()->format('Y-m-d'),
            'system.time' => now()->format('H:i'),
            'system.datetime' => now()->format('Y-m-d H:i'),
            'system.year' => now()->year,
            'system.month' => now()->format('F'),
            'system.day' => now()->format('l'),
            'user.name' => auth()->user()?->name,
            'user.email' => auth()->user()?->email,
        ];
    }

    /**
     * Replace variables in content
     */
    protected function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Support multiple variable formats: {{var}}, {var}, [var]
            $patterns = [
                '{{' . $key . '}}',
                '{' . $key . '}',
                '[' . $key . ']',
            ];
            
            foreach ($patterns as $pattern) {
                $content = str_replace($pattern, $value ?? '', $content);
            }
        }
        
        return $content;
    }

    /**
     * Apply personalization rules
     */
    protected function applyPersonalizationRules(string $content, array $rules, $entity): string
    {
        foreach ($rules as $rule) {
            $condition = $rule['condition'] ?? null;
            $replacement = $rule['replacement'] ?? null;
            $target = $rule['target'] ?? null;
            
            if (!$condition || !$replacement || !$target) {
                continue;
            }
            
            // Evaluate condition
            if ($this->evaluateCondition($condition, $entity)) {
                $content = str_replace($target, $replacement, $content);
            }
        }
        
        return $content;
    }

    /**
     * Evaluate personalization condition
     */
    protected function evaluateCondition(array $condition, $entity): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? null;
        
        if (!$field) {
            return false;
        }
        
        // Get field value from entity
        $fieldValue = data_get($entity, $field);
        
        return match ($operator) {
            '=' => $fieldValue == $value,
            '!=' => $fieldValue != $value,
            '>' => $fieldValue > $value,
            '<' => $fieldValue < $value,
            '>=' => $fieldValue >= $value,
            '<=' => $fieldValue <= $value,
            'contains' => str_contains($fieldValue, $value),
            'starts_with' => str_starts_with($fieldValue, $value),
            'ends_with' => str_ends_with($fieldValue, $value),
            'in' => in_array($fieldValue, (array) $value),
            default => false,
        };
    }

    /**
     * Apply conditional content blocks
     */
    protected function applyConditionalContent(string $content, array $variables): string
    {
        // Handle IF blocks: {% if variable %}content{% endif %}
        $pattern = '/{%\s*if\s+([a-zA-Z0-9._]+)\s*%}(.*?){%\s*endif\s*%}/s';
        
        $content = preg_replace_callback($pattern, function ($matches) use ($variables) {
            $variable = $matches[1];
            $conditionalContent = $matches[2];
            
            // Check if variable exists and is truthy
            $value = $variables[$variable] ?? null;
            
            return $value ? $conditionalContent : '';
        }, $content);
        
        // Handle IF-ELSE blocks: {% if variable %}content{% else %}alternative{% endif %}
        $pattern = '/{%\s*if\s+([a-zA-Z0-9._]+)\s*%}(.*?){%\s*else\s*%}(.*?){%\s*endif\s*%}/s';
        
        $content = preg_replace_callback($pattern, function ($matches) use ($variables) {
            $variable = $matches[1];
            $trueContent = $matches[2];
            $falseContent = $matches[3];
            
            $value = $variables[$variable] ?? null;
            
            return $value ? $trueContent : $falseContent;
        }, $content);
        
        return $content;
    }

    /**
     * Preview dynamic content with sample data
     */
    public function preview(DynamicContentTemplate $template, array $sampleData = []): string
    {
        // Create a mock entity for preview
        $mockContact = new Contact([
            'first_name' => $sampleData['first_name'] ?? 'John',
            'last_name' => $sampleData['last_name'] ?? 'Doe',
            'email' => $sampleData['email'] ?? 'john.doe@example.com',
            'title' => $sampleData['title'] ?? 'CEO',
            'city' => $sampleData['city'] ?? 'Stockholm',
            'country' => $sampleData['country'] ?? 'Sweden',
        ]);
        
        return $this->render($template, $mockContact, $sampleData);
    }

    /**
     * Validate template syntax
     */
    public function validateTemplate(string $content): array
    {
        $errors = [];
        
        // Check for unclosed variable tags
        $openBraces = substr_count($content, '{{');
        $closeBraces = substr_count($content, '}}');
        
        if ($openBraces !== $closeBraces) {
            $errors[] = 'Mismatched variable braces {{ }}';
        }
        
        // Check for unclosed conditional blocks
        $ifCount = preg_match_all('/{%\s*if\s+/', $content);
        $endifCount = preg_match_all('/{%\s*endif\s*%}/', $content);
        
        if ($ifCount !== $endifCount) {
            $errors[] = 'Mismatched if/endif blocks';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Extract all variables used in template
     */
    public function extractTemplateVariables(string $content): array
    {
        $variables = [];
        
        // Extract {{variable}} format
        preg_match_all('/{{([a-zA-Z0-9._]+)}}/', $content, $matches);
        $variables = array_merge($variables, $matches[1]);
        
        // Extract {variable} format
        preg_match_all('/{([a-zA-Z0-9._]+)}/', $content, $matches);
        $variables = array_merge($variables, $matches[1]);
        
        // Extract [variable] format
        preg_match_all('/\[([a-zA-Z0-9._]+)\]/', $content, $matches);
        $variables = array_merge($variables, $matches[1]);
        
        // Extract {% if variable %} format
        preg_match_all('/{%\s*if\s+([a-zA-Z0-9._]+)\s*%}/', $content, $matches);
        $variables = array_merge($variables, $matches[1]);
        
        return array_unique($variables);
    }
}
