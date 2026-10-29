<?php

namespace App\Livewire;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class KnowledgeBaseManager extends Component
{
    use WithPagination;

    // View state
    public string $view = 'list'; // list, create, edit, show

    // Form fields
    public ?int $editingArticleId = null;
    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $metaDescription = '';
    public string $category = '';
    public string $status = 'draft';
    public array $tags = [];
    public string $newTag = '';

    // Filters
    #[Url]
    public string $search = '';

    #[Url]
    public string $categoryFilter = '';

    #[Url]
    public string $statusFilter = '';

    // Preview article
    public ?KnowledgeBaseArticle $previewArticle = null;

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?int $deletingArticleId = null;

    protected function rules(): array
    {
        $slugRule = $this->editingArticleId
            ? "required|min:3|max:255|unique:knowledge_base_articles,slug,{$this->editingArticleId}"
            : 'required|min:3|max:255|unique:knowledge_base_articles,slug';

        return [
            'title' => 'required|min:5|max:255',
            'slug' => $slugRule,
            'content' => 'required|min:50',
            'metaDescription' => 'nullable|max:500',
            'category' => 'required|min:3|max:100',
            'status' => 'required|in:draft,published,archived',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ];
    }

    protected $messages = [
        'title.required' => 'Artikelrubriken ar obligatorisk.',
        'title.min' => 'Rubriken maste vara minst 5 tecken.',
        'slug.required' => 'URL-slug ar obligatorisk.',
        'slug.unique' => 'Denna URL-slug anvands redan.',
        'content.required' => 'Artikelinnehall ar obligatoriskt.',
        'content.min' => 'Innehallet maste vara minst 50 tecken.',
        'category.required' => 'Kategori ar obligatorisk.',
    ];

    public function mount(): void
    {
        // Default view is list
    }

    #[Computed]
    public function articles()
    {
        return KnowledgeBaseArticle::with(['creator', 'updater'])
            ->when($this->search, fn ($query) => $query->search($this->search))
            ->when($this->categoryFilter, fn ($query) => $query->inCategory($this->categoryFilter))
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return KnowledgeBaseArticle::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort()
            ->values();
    }

    #[Computed]
    public function stats()
    {
        return [
            'total_articles' => KnowledgeBaseArticle::count(),
            'published' => KnowledgeBaseArticle::published()->count(),
            'drafts' => KnowledgeBaseArticle::draft()->count(),
            'total_views' => KnowledgeBaseArticle::sum('view_count'),
            'avg_helpfulness' => KnowledgeBaseArticle::whereRaw('(helpful_votes + unhelpful_votes) > 0')
                ->selectRaw('AVG(helpful_votes / (helpful_votes + unhelpful_votes) * 100) as avg')
                ->value('avg') ?? 0,
        ];
    }

    public function render()
    {
        return view('livewire.knowledge-base-manager');
    }

    // Navigation methods
    public function showList(): void
    {
        $this->view = 'list';
        $this->resetForm();
    }

    public function showCreate(): void
    {
        $this->resetForm();
        $this->view = 'create';
    }

    public function showEdit(int $articleId): void
    {
        $article = KnowledgeBaseArticle::findOrFail($articleId);
        $this->editingArticleId = $article->id;
        $this->title = $article->title;
        $this->slug = $article->slug;
        $this->content = $article->content;
        $this->metaDescription = $article->meta_description ?? '';
        $this->category = $article->category;
        $this->status = $article->status;
        $this->tags = $article->tags ?? [];
        $this->view = 'edit';
    }

    public function showPreview(int $articleId): void
    {
        $article = KnowledgeBaseArticle::with(['creator', 'updater'])->findOrFail($articleId);
        $article->incrementViews();
        $this->previewArticle = $article;
        $this->view = 'show';
    }

    // CRUD methods
    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'meta_description' => $this->metaDescription ?: null,
            'category' => $this->category,
            'status' => $this->status,
            'tags' => $this->tags,
            'updated_by' => auth()->id(),
            'ai_keywords' => $this->generateAiKeywords($this->content),
            'ai_relevance_score' => $this->calculateAiRelevanceScore($this->content, $this->title, $this->category),
        ];

        if ($this->editingArticleId) {
            $article = KnowledgeBaseArticle::findOrFail($this->editingArticleId);
            $article->update($data);
            $this->dispatch('notify', message: 'Artikeln har uppdaterats!', type: 'success');
        } else {
            $data['created_by'] = auth()->id();
            KnowledgeBaseArticle::create($data);
            $this->dispatch('notify', message: 'Artikeln har skapats!', type: 'success');
        }

        $this->showList();
    }

    public function confirmDelete(int $articleId): void
    {
        $this->deletingArticleId = $articleId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->deletingArticleId = null;
        $this->showDeleteModal = false;
    }

    public function deleteArticle(): void
    {
        if ($this->deletingArticleId) {
            KnowledgeBaseArticle::findOrFail($this->deletingArticleId)->delete();
            $this->dispatch('notify', message: 'Artikeln har raderats!', type: 'success');
        }

        $this->cancelDelete();
        $this->resetPage();
    }

    public function toggleStatus(int $articleId, string $newStatus): void
    {
        $article = KnowledgeBaseArticle::findOrFail($articleId);
        $article->update(['status' => $newStatus, 'updated_by' => auth()->id()]);

        $statusText = match ($newStatus) {
            'published' => 'publicerad',
            'draft' => 'utkast',
            'archived' => 'arkiverad',
            default => $newStatus,
        };

        $this->dispatch('notify', message: "Artikeln ar nu {$statusText}!", type: 'success');
    }

    public function duplicateArticle(int $articleId): void
    {
        $original = KnowledgeBaseArticle::findOrFail($articleId);

        $newArticle = $original->replicate();
        $newArticle->title = $original->title . ' (Kopia)';
        $newArticle->slug = KnowledgeBaseArticle::generateUniqueSlug($newArticle->title);
        $newArticle->status = 'draft';
        $newArticle->view_count = 0;
        $newArticle->helpful_votes = 0;
        $newArticle->unhelpful_votes = 0;
        $newArticle->created_by = auth()->id();
        $newArticle->updated_by = auth()->id();
        $newArticle->save();

        $this->dispatch('notify', message: 'Artikeln har duplicerats!', type: 'success');
        $this->resetPage();
    }

    // Voting methods
    public function voteHelpful(int $articleId): void
    {
        $article = KnowledgeBaseArticle::findOrFail($articleId);
        $article->addHelpfulVote();
        $this->previewArticle = $article->fresh();
        $this->dispatch('notify', message: 'Tack for din feedback!', type: 'success');
    }

    public function voteUnhelpful(int $articleId): void
    {
        $article = KnowledgeBaseArticle::findOrFail($articleId);
        $article->addUnhelpfulVote();
        $this->previewArticle = $article->fresh();
        $this->dispatch('notify', message: 'Tack for din feedback!', type: 'success');
    }

    // Tag management
    public function addTag(): void
    {
        $tag = trim($this->newTag);

        if ($tag && ! in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }

        $this->newTag = '';
    }

    public function removeTag(int $index): void
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    // Slug generation
    public function updatedTitle(): void
    {
        if (! $this->editingArticleId || empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->title);
    }

    // Filters
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    // Helper methods
    private function resetForm(): void
    {
        $this->editingArticleId = null;
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->metaDescription = '';
        $this->category = '';
        $this->status = 'draft';
        $this->tags = [];
        $this->newTag = '';
        $this->previewArticle = null;
        $this->resetValidation();
    }

    private function generateAiKeywords(string $content): array
    {
        try {
            $aiService = new \App\Services\AiService();
            $config = \App\Models\UserAiConfiguration::active()->default()->first();
            
            if ($config) {
                $prompt = "Extract 5-10 relevant keywords from this knowledge base article content. Return only a JSON array of keywords, nothing else:\n\n" . substr(strip_tags($content), 0, 2000);
                
                $response = $aiService->generateText($config, $prompt);
                
                if ($response && isset($response['content'])) {
                    $keywords = json_decode($response['content'], true);
                    if (is_array($keywords)) {
                        return array_slice($keywords, 0, 10);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('AI keyword generation failed, using fallback', ['error' => $e->getMessage()]);
        }
        
        // Fallback: Extract meaningful keywords from content
        $words = str_word_count(strip_tags($content), 1);
        $stopWords = ['this', 'that', 'with', 'have', 'will', 'from', 'they', 'been', 'said', 'each', 'which', 'their', 'time', 'about', 'when', 'also', 'may', 'more', 'some', 'only', 'can', 'than', 'into', 'what', 'other', 'were', 'could', 'after', 'first', 'well', 'even', 'most', 'such', 'many', 'must', 'through', 'where', 'much', 'back', 'them', 'these', 'those', 'being', 'does', 'were', 'said', 'each', 'which', 'their', 'time', 'about', 'when', 'also', 'may', 'more', 'some', 'only', 'can', 'than', 'into', 'what', 'other', 'were', 'could', 'after', 'first', 'well', 'even', 'most', 'such', 'many', 'must', 'through', 'where', 'much', 'back', 'them', 'these', 'those', 'being', 'does'];
        
        $meaningfulWords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array(strtolower($word), $stopWords);
        });
        
        $wordFrequency = array_count_values($meaningfulWords);
        arsort($wordFrequency);
        
        return array_slice(array_keys($wordFrequency), 0, 10);
    }

    private function calculateAiRelevanceScore(string $content, string $title, string $category): float
    {
        try {
            $aiService = new \App\Services\AiService();
            $config = \App\Models\UserAiConfiguration::active()->default()->first();
            
            if ($config) {
                $prompt = "Analyze this knowledge base article and calculate a relevance score from 0.0 to 1.0 based on:
1. Content quality and completeness
2. Title clarity and relevance
3. Category appropriateness
4. Overall value to readers

Return only a single decimal number between 0.0 and 1.0, nothing else.

Title: {$title}
Category: {$category}
Content: " . substr(strip_tags($content), 0, 1500);
                
                $response = $aiService->generateText($config, $prompt);
                
                if ($response && isset($response['content'])) {
                    $score = (float) trim($response['content']);
                    if ($score >= 0.0 && $score <= 1.0) {
                        return round($score, 2);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('AI relevance score calculation failed, using fallback', ['error' => $e->getMessage()]);
        }
        
        // Fallback: Calculate relevance score based on content metrics
        $contentLength = strlen(strip_tags($content));
        $titleLength = strlen($title);
        $wordCount = str_word_count(strip_tags($content));
        
        // Base score from content length (optimal 500-2000 words)
        $lengthScore = 0.0;
        if ($wordCount >= 500 && $wordCount <= 2000) {
            $lengthScore = 0.4;
        } elseif ($wordCount >= 200 && $wordCount < 500) {
            $lengthScore = 0.3;
        } elseif ($wordCount > 2000) {
            $lengthScore = 0.35;
        } else {
            $lengthScore = 0.1;
        }
        
        // Title quality score
        $titleScore = min(0.3, $titleLength / 100);
        
        // Category relevance (if category is specific and meaningful)
        $categoryScore = (strlen($category) > 5 && !in_array(strtolower($category), ['general', 'other', 'misc'])) ? 0.3 : 0.1;
        
        $totalScore = $lengthScore + $titleScore + $categoryScore;
        
        return min(1.0, max(0.1, round($totalScore, 2)));
    }
}
