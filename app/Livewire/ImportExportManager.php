<?php

namespace App\Livewire;

use App\Models\ImportJob;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImportExportManager extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'import';
    public $search = '';
    public $showImportModal = false;
    
    // Import properties
    public $file;
    public $importType = 'contacts';
    public $hasHeaders = true;
    public $columnMappings = [];
    public $previewData = [];
    
    protected $queryString = ['search', 'activeTab'];
    
    protected $rules = [
        'file' => 'required|mimes:csv,xlsx,xls|max:10240', // 10MB max
        'importType' => 'required|in:contacts,companies,deals',
    ];

    public function mount($action = null)
    {
        if ($action === 'import') {
            $this->showImportModal = true;
        }
    }

    public function showImportModal()
    {
        $this->showImportModal = true;
        $this->resetImportForm();
    }

    public function hideImportModal()
    {
        $this->showImportModal = false;
        $this->resetImportForm();
    }

    public function resetImportForm()
    {
        $this->file = null;
        $this->importType = 'contacts';
        $this->hasHeaders = true;
        $this->columnMappings = [];
        $this->previewData = [];
        $this->resetErrorBag();
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240'
        ]);

        try {
            $this->previewData = $this->parseFilePreview();
        } catch (\Exception $e) {
            $this->addError('file', 'Failed to read file: ' . $e->getMessage());
        }
    }

    private function parseFilePreview()
    {
        $path = $this->file->store('temp-imports');
        $extension = $this->file->getClientOriginalExtension();
        
        if ($extension === 'csv') {
            return $this->parseCsvPreview(Storage::path($path));
        } else {
            // For Excel files, we would use a library like PhpSpreadsheet
            // For now, return a mock preview
            return [
                'headers' => ['Name', 'Email', 'Phone', 'Company'],
                'rows' => [
                    ['John Doe', 'john@example.com', '555-1234', 'Acme Corp'],
                    ['Jane Smith', 'jane@example.com', '555-5678', 'Tech Inc'],
                ]
            ];
        }
    }

    private function parseCsvPreview($path)
    {
        $handle = fopen($path, 'r');
        $preview = ['headers' => [], 'rows' => []];
        
        if ($handle !== false) {
            $lineCount = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false && $lineCount < 6) {
                if ($lineCount === 0 && $this->hasHeaders) {
                    $preview['headers'] = $data;
                } else {
                    $preview['rows'][] = $data;
                }
                $lineCount++;
            }
            fclose($handle);
        }
        
        // If no headers specified, create generic ones
        if (empty($preview['headers']) && !empty($preview['rows'])) {
            $preview['headers'] = array_map(function($i) {
                return "Column " . ($i + 1);
            }, array_keys($preview['rows'][0]));
        }
        
        return $preview;
    }

    public function startImport()
    {
        $this->validate();

        if (empty($this->columnMappings)) {
            throw ValidationException::withMessages(['columnMappings' => 'Please map at least one column.']);
        }

        try {
            // Store the file permanently
            $filePath = $this->file->store('imports');
            
            // Create import job
            $importJob = ImportJob::create([
                'user_id' => auth()->id(),
                'type' => $this->importType,
                'filename' => $this->file->getClientOriginalName(),
                'file_path' => $filePath,
                'column_mappings' => json_encode($this->columnMappings),
                'has_headers' => $this->hasHeaders,
                'status' => 'pending',
            ]);

            // In a real application, this would be dispatched to a queue
            // dispatch(new ProcessImportJob($importJob));

            session()->flash('message', 'Import job created successfully! Processing will begin shortly.');
            $this->hideImportModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to start import: ' . $e->getMessage());
        }
    }

    public function exportData($type)
    {
        try {
            // In a real application, this would generate and download a file
            // For now, we'll just show a success message
            session()->flash('message', "Export of {$type} data initiated. You'll receive an email when it's ready.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to start export: ' . $e->getMessage());
        }
    }

    public function deleteImportJob($jobId)
    {
        try {
            $job = ImportJob::findOrFail($jobId);
            
            // Delete the file if it exists
            if ($job->file_path && Storage::exists($job->file_path)) {
                Storage::delete($job->file_path);
            }
            
            $job->delete();
            session()->flash('message', 'Import job deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete import job: ' . $e->getMessage());
        }
    }

    public function retryImportJob($jobId)
    {
        try {
            $job = ImportJob::findOrFail($jobId);
            $job->update(['status' => 'pending', 'error_message' => null]);
            
            // In a real application, re-dispatch to queue
            // dispatch(new ProcessImportJob($job));
            
            session()->flash('message', 'Import job queued for retry!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to retry import job: ' . $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getImportJobsProperty()
    {
        $query = ImportJob::query()
            ->where('user_id', auth()->id());

        if ($this->search) {
            $query->where('filename', 'like', '%' . $this->search . '%')
                  ->orWhere('type', 'like', '%' . $this->search . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAvailableFieldsProperty()
    {
        $fields = [
            'contacts' => [
                'name' => 'Full Name',
                'first_name' => 'First Name', 
                'last_name' => 'Last Name',
                'email' => 'Email Address',
                'phone' => 'Phone Number',
                'company_name' => 'Company Name',
                'title' => 'Job Title',
                'address' => 'Address',
                'city' => 'City',
                'state' => 'State',
                'zip_code' => 'ZIP Code',
                'country' => 'Country',
            ],
            'companies' => [
                'name' => 'Company Name',
                'email' => 'Email Address', 
                'phone' => 'Phone Number',
                'website' => 'Website',
                'industry' => 'Industry',
                'employee_count' => 'Employee Count',
                'annual_revenue' => 'Annual Revenue',
                'address' => 'Address',
                'city' => 'City',
                'state' => 'State',
                'zip_code' => 'ZIP Code',
                'country' => 'Country',
            ],
            'deals' => [
                'title' => 'Deal Title',
                'value' => 'Deal Value',
                'contact_name' => 'Contact Name',
                'company_name' => 'Company Name',
                'stage' => 'Pipeline Stage',
                'probability' => 'Probability',
                'expected_close_date' => 'Expected Close Date',
                'source' => 'Deal Source',
                'description' => 'Description',
            ]
        ];

        return $fields[$this->importType] ?? [];
    }

    public function render()
    {
        return view('livewire.import-export-manager', [
            'importJobs' => $this->importJobs,
            'availableFields' => $this->availableFields,
        ]);
    }
}