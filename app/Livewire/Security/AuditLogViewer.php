<?php

namespace App\Livewire\Security;

use App\Models\SecurityAuditLog;
use App\Services\Security\AuditTrailService;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    public $filters = [
        'event_type' => '',
        'event_category' => '',
        'risk_level' => '',
        'user_id' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    public $selectedLog = null;
    public $showDetailsModal = false;

    public function mount()
    {
        $this->filters['date_from'] = now()->subDays(30)->format('Y-m-d');
        $this->filters['date_to'] = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = SecurityAuditLog::with('user')
            ->orderBy('occurred_at', 'desc');

        // Apply filters
        if ($this->filters['event_type']) {
            $query->where('event_type', $this->filters['event_type']);
        }

        if ($this->filters['event_category']) {
            $query->where('event_category', $this->filters['event_category']);
        }

        if ($this->filters['risk_level']) {
            $query->where('risk_level', $this->filters['risk_level']);
        }

        if ($this->filters['user_id']) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if ($this->filters['date_from']) {
            $query->where('occurred_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->where('occurred_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        $logs = $query->paginate(20);

        // Get summary statistics
        $stats = $this->getAuditStats();

        return view('livewire.security.audit-log-viewer', [
            'logs' => $logs,
            'stats' => $stats,
        ]);
    }

    public function showDetails($logId)
    {
        $this->selectedLog = SecurityAuditLog::with(['user', 'auditable'])->find($logId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedLog = null;
    }

    public function exportLogs()
    {
        // Apply the same filters as the view
        $query = SecurityAuditLog::with('user')
            ->orderBy('occurred_at', 'desc');

        // Apply filters (same logic as render method)
        if ($this->filters['event_type']) {
            $query->where('event_type', $this->filters['event_type']);
        }

        if ($this->filters['event_category']) {
            $query->where('event_category', $this->filters['event_category']);
        }

        if ($this->filters['risk_level']) {
            $query->where('risk_level', $this->filters['risk_level']);
        }

        if ($this->filters['user_id']) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if ($this->filters['date_from']) {
            $query->where('occurred_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->where('occurred_at', '<=', $this->filters['date_to'] . ' 23:59:59');
        }

        $logs = $query->get();

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($handle, [
                'ID',
                'Date/Time',
                'User',
                'Event Type',
                'Category',
                'Risk Level',
                'Description',
                'IP Address',
                'User Agent'
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->occurred_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'System',
                    $log->event_type,
                    $log->event_category,
                    $log->risk_level,
                    $log->description,
                    $log->ip_address,
                    $log->user_agent,
                ]);
            }

            fclose($handle);
        }, 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv');
    }

    public function clearFilters()
    {
        $this->filters = [
            'event_type' => '',
            'event_category' => '',
            'risk_level' => '',
            'user_id' => '',
            'date_from' => now()->subDays(30)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ];
    }

    protected function getAuditStats(): array
    {
        $auditService = app(AuditTrailService::class);
        
        return [
            'total_events' => SecurityAuditLog::count(),
            'high_risk_events' => SecurityAuditLog::highRisk()->recent(7)->count(),
            'recent_logins' => SecurityAuditLog::forEventType('login_success')->recent(1)->count(),
            'failed_attempts' => SecurityAuditLog::forEventType('login_failed')->recent(7)->count(),
            'compliance_report' => $auditService->getComplianceReport(30),
        ];
    }
}