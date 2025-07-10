<?php

namespace App\Livewire;

use App\Models\SalesContent;
use App\Models\SalesContentCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SalesContentLibrary extends Component
{
    use WithPagination, WithFileUploads;

    public $showCreateModal = false;
    public $editingContent = null;
    public $selectedContent = null;
    
    // Form fields
    public $title = '';
    public $description = '';
    public $type = 'document';
    public $category_id = '';
    public $content = '';
    public $tags = [];
    public $status = 'draft';
    public $uploadedFile = null;
    
    // Search and filters
    public $search = '';
    public $typeFilter = '';
    public $categoryFilter = '';
    public $statusFilter = '';

    public function render()
    {
        $salesContent = SalesContent::with(['creator', 'category'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = SalesContentCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.sales-enablement.content-library', [
            'salesContent' => $salesContent,
            'categories' => $categories,
        ]);
    }

    public function createContent()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function editContent(SalesContent $content)
    {
        $this->editingContent = $content;
        $this->title = $content->title;
        $this->description = $content->description;
        $this->type = $content->type;
        $this->category_id = $content->category_id;
        $this->content = $content->content;
        $this->tags = $content->tags ?? [];
        $this->status = $content->status;
        $this->showCreateModal = true;
    }

    public function viewContent(SalesContent $content)
    {
        $this->selectedContent = $content;
        
        // Record view analytics
        $content->recordView(auth()->user());
    }

    public function downloadContent(SalesContent $content)
    {
        // Record download analytics
        $content->recordDownload(auth()->user());
        
        if ($content->file_path) {
            return response()->download(storage_path('app/public/' . $content->file_path));
        }
    }

    public function saveContent()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document,presentation,video,image,template,battle_card',
            'category_id' => 'nullable|exists:sales_content_categories,id',
            'content' => 'nullable|string',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published,archived',
            'uploadedFile' => 'nullable|file|max:10240', // 10MB max
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'content' => $this->content,
            'tags' => array_filter($this->tags),
            'status' => $this->status,
            'created_by' => auth()->id(),
        ];

        // Handle file upload
        if ($this->uploadedFile instanceof TemporaryUploadedFile) {
            $path = $this->uploadedFile->store('sales-content', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $this->uploadedFile->getClientOriginalName();
            $data['file_size'] = $this->uploadedFile->getSize();
            $data['mime_type'] = $this->uploadedFile->getMimeType();
        }

        if ($this->editingContent) {
            $this->editingContent->update($data);
            session()->flash('message', 'Content updated successfully.');
        } else {
            SalesContent::create($data);
            session()->flash('message', 'Content created successfully.');
        }

        $this->resetForm();
        $this->showCreateModal = false;
    }

    public function deleteContent(SalesContent $content)
    {
        // Delete file if exists
        if ($content->file_path && \Storage::disk('public')->exists($content->file_path)) {
            \Storage::disk('public')->delete($content->file_path);
        }
        
        $content->delete();
        session()->flash('message', 'Content deleted successfully.');
    }

    public function rateContent(SalesContent $content, int $rating)
    {
        $content->addRating(auth()->user(), $rating);
        session()->flash('message', 'Thank you for your rating!');
    }

    public function addTag()
    {
        $this->tags[] = '';
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->selectedContent = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingContent = null;
        $this->title = '';
        $this->description = '';
        $this->type = 'document';
        $this->category_id = '';
        $this->content = '';
        $this->tags = [];
        $this->status = 'draft';
        $this->uploadedFile = null;
    }
}