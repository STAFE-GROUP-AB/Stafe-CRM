<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KnowledgeBaseArticle;

class KnowledgeBaseManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $editingArticle = null;
    public $title = '';
    public $content = '';
    public $category = '';
    public $tags = [];
    public $isPublished = true;
    public $search = '';
    public $categoryFilter = '';

    protected $rules = [
        'title' => 'required|min:5|max:255',
        'content' => 'required|min:50',
        'category' => 'required|min:3|max:100',
        'isPublished' => 'boolean'
    ];

    public function render()
    {
        $articles = KnowledgeBaseArticle::with(['creator', 'updater'])
            ->when($this->search, fn($query) => $query->search($this->search))
            ->when($this->categoryFilter, fn($query) => 
                $query->where('category', $this->categoryFilter)
            )
            ->latest()
            ->paginate(10);

        $categories = KnowledgeBaseArticle::select('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        $stats = [
            'total_articles' => KnowledgeBaseArticle::count(),
            'published' => KnowledgeBaseArticle::published()->count(),
            'total_views' => KnowledgeBaseArticle::sum('view_count'),
            'avg_helpfulness' => KnowledgeBaseArticle::whereRaw('(helpful_votes + unhelpful_votes) > 0')
                ->selectRaw('AVG(helpful_votes / (helpful_votes + unhelpful_votes) * 100) as avg')
                ->value('avg') ?? 0
        ];

        return view('livewire.knowledge-base-manager', compact('articles', 'categories', 'stats'));
    }

    public function createArticle()
    {
        $this->showCreateModal = true;
        $this->reset(['title', 'content', 'category', 'tags', 'isPublished']);
    }

    public function editArticle($articleId)
    {
        $article = KnowledgeBaseArticle::findOrFail($articleId);
        $this->editingArticle = $article;
        $this->title = $article->title;
        $this->content = $article->content;
        $this->category = $article->category;
        $this->tags = $article->tags ?? [];
        $this->isPublished = $article->is_published;
        $this->showCreateModal = true;
    }

    public function saveArticle()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category,
            'tags' => $this->tags,
            'is_published' => $this->isPublished,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            // AI keywords would be generated here in real implementation
            'ai_keywords' => $this->generateAiKeywords($this->content),
            'ai_relevance_score' => rand(70, 100) / 100 // Mock AI score
        ];

        if ($this->editingArticle) {
            $this->editingArticle->update($data);
            $this->dispatch('article-updated', articleId: $this->editingArticle->id);
        } else {
            KnowledgeBaseArticle::create($data);
            $this->dispatch('article-created');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function deleteArticle($articleId)
    {
        KnowledgeBaseArticle::findOrFail($articleId)->delete();
        $this->dispatch('article-deleted');
        $this->resetPage();
    }

    public function togglePublished($articleId)
    {
        $article = KnowledgeBaseArticle::findOrFail($articleId);
        $article->update(['is_published' => !$article->is_published]);
        $this->dispatch('article-toggled');
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->editingArticle = null;
        $this->reset(['title', 'content', 'category', 'tags', 'isPublished']);
    }

    public function addTag($tag)
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    private function generateAiKeywords($content): array
    {
        // Mock AI keyword generation - in real implementation, this would use ML/AI
        $words = str_word_count($content, 1);
        $keywords = array_slice(array_unique($words), 0, 10);
        return array_filter($keywords, fn($word) => strlen($word) > 3);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
}