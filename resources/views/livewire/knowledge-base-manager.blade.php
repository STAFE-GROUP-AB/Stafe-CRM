<div class="space-y-6">
    {{-- Page Header --}}
    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kunskapsbas</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Hantera artiklar och dokumentation for ditt team</p>
            </div>
            @if($view === 'list')
                <button
                    wire:click="showCreate"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ny artikel
                </button>
            @else
                <button
                    wire:click="showList"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Tillbaka till listan
                </button>
            @endif
        </div>
    </div>

    {{-- Statistics Cards --}}
    @if($view === 'list')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Totalt artiklar</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->stats['total_articles'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Publicerade</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->stats['published'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Visningar</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->stats['total_views']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hjalpsam</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->stats['avg_helpfulness'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content Area --}}
    @if($view === 'list')
        {{-- List View --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Filters --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    {{-- Search --}}
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Sok artiklar..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            >
                        </div>
                    </div>

                    {{-- Category Filter --}}
                    <div class="w-full md:w-48">
                        <select
                            wire:model.live="categoryFilter"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="">Alla kategorier</option>
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="w-full md:w-40">
                        <select
                            wire:model.live="statusFilter"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                            <option value="">Alla status</option>
                            <option value="published">Publicerad</option>
                            <option value="draft">Utkast</option>
                            <option value="archived">Arkiverad</option>
                        </select>
                    </div>

                    {{-- Clear Filters --}}
                    @if($search || $categoryFilter || $statusFilter)
                        <button
                            wire:click="clearFilters"
                            class="inline-flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Rensa
                        </button>
                    @endif
                </div>
            </div>

            {{-- Articles Table --}}
            @if($this->articles->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Artikel
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Statistik
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Uppdaterad
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Atgarder</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->articles as $article)
                                <tr wire:key="article-{{ $article->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <button
                                                    wire:click="showPreview({{ $article->id }})"
                                                    class="text-sm font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 text-left"
                                                >
                                                    {{ $article->title }}
                                                </button>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                                                    {{ $article->excerpt }}
                                                </p>
                                                @if($article->tags && count($article->tags) > 0)
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach(array_slice($article->tags, 0, 3) as $tag)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                                                {{ $tag }}
                                                            </span>
                                                        @endforeach
                                                        @if(count($article->tags) > 3)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">+{{ count($article->tags) - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $article->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'published' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                                'draft' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                                'archived' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                            ];
                                            $statusLabels = [
                                                'published' => 'Publicerad',
                                                'draft' => 'Utkast',
                                                'archived' => 'Arkiverad',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$article->status] ?? $statusColors['draft'] }}">
                                            {{ $statusLabels[$article->status] ?? $article->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <div class="flex items-center gap-3">
                                                <span class="flex items-center text-gray-500 dark:text-gray-400" title="Visningar">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    {{ number_format($article->view_count) }}
                                                </span>
                                                @if($article->total_votes > 0)
                                                    <span class="flex items-center {{ $article->helpfulness_ratio >= 50 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" title="Hjalpsam">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                                        </svg>
                                                        {{ $article->helpfulness_ratio }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $article->updated_at->diffForHumans() }}
                                        </div>
                                        @if($article->updater)
                                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                                av {{ $article->updater->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Quick Status Toggle --}}
                                            <div x-data="{ open: false }" class="relative">
                                                <button
                                                    @click="open = !open"
                                                    class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                                    title="Andra status"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                    </svg>
                                                </button>
                                                <div
                                                    x-show="open"
                                                    @click.away="open = false"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50"
                                                    style="display: none;"
                                                >
                                                    <div class="py-1">
                                                        @if($article->status !== 'published')
                                                            <button
                                                                wire:click="toggleStatus({{ $article->id }}, 'published')"
                                                                @click="open = false"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                                                            >
                                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Publicera
                                                            </button>
                                                        @endif
                                                        @if($article->status !== 'draft')
                                                            <button
                                                                wire:click="toggleStatus({{ $article->id }}, 'draft')"
                                                                @click="open = false"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                                                            >
                                                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                                Gor till utkast
                                                            </button>
                                                        @endif
                                                        @if($article->status !== 'archived')
                                                            <button
                                                                wire:click="toggleStatus({{ $article->id }}, 'archived')"
                                                                @click="open = false"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                                                            >
                                                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                                </svg>
                                                                Arkivera
                                                            </button>
                                                        @endif
                                                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                                                        <button
                                                            wire:click="duplicateArticle({{ $article->id }})"
                                                            @click="open = false"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                                                        >
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Duplicera
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Edit Button --}}
                                            <button
                                                wire:click="showEdit({{ $article->id }})"
                                                class="p-2 text-gray-400 hover:text-blue-600 dark:text-gray-500 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                                title="Redigera"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            {{-- Delete Button --}}
                                            <button
                                                wire:click="confirmDelete({{ $article->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:text-gray-500 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                                title="Radera"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $this->articles->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    @if($search || $categoryFilter || $statusFilter)
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Inga artiklar hittades</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Prova att justera dina filter eller sok efter nagot annat.</p>
                        <button
                            wire:click="clearFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition-colors"
                        >
                            Rensa filter
                        </button>
                    @else
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Inga artiklar annu</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Borja bygga din kunskapsbas genom att skapa din forsta artikel.</p>
                        <button
                            wire:click="showCreate"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Skapa forsta artikeln
                        </button>
                    @endif
                </div>
            @endif
        </div>

    @elseif($view === 'create' || $view === 'edit')
        {{-- Create/Edit Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $view === 'create' ? 'Skapa ny artikel' : 'Redigera artikel' }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $view === 'create' ? 'Fyll i informationen nedan for att skapa en ny kunskapsbasartikel.' : 'Uppdatera informationen nedan.' }}
                </p>
            </div>

            <form wire:submit="save" class="p-6 space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rubrik <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        wire:model.live.debounce.500ms="title"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ange artikelns rubrik"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        URL-slug <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            id="slug"
                            wire:model="slug"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="artikel-url-slug"
                        >
                        <button
                            type="button"
                            wire:click="generateSlug"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors text-sm"
                        >
                            Generera
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Kommer visas i URL:en som: /kunskapsbas/{{ $slug ?: 'artikel-url-slug' }}
                    </p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category and Status Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="category"
                            wire:model="category"
                            list="category-suggestions"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="T.ex. Guider, FAQ, Dokumentation"
                        >
                        <datalist id="category-suggestions">
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            wire:model="status"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="draft">Utkast</option>
                            <option value="published">Publicerad</option>
                            <option value="archived">Arkiverad</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Meta Description --}}
                <div>
                    <label for="metaDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Metabeskrivning
                    </label>
                    <textarea
                        id="metaDescription"
                        wire:model="metaDescription"
                        rows="2"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Kort sammanfattning av artikeln for sokresultat..."
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Max 500 tecken. Visas i sokresultat och forhandsvisningar.
                    </p>
                    @error('metaDescription')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tags --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Taggar
                    </label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach($tags as $index => $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ $tag }}
                                <button
                                    type="button"
                                    wire:click="removeTag({{ $index }})"
                                    class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model="newTag"
                            wire:keydown.enter.prevent="addTag"
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                            placeholder="Lagg till tagg och tryck Enter"
                        >
                        <button
                            type="button"
                            wire:click="addTag"
                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors text-sm"
                        >
                            Lagg till
                        </button>
                    </div>
                </div>

                {{-- Content --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Innehall <span class="text-red-500">*</span>
                    </label>
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900/50 px-3 py-2 border-b border-gray-300 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Stodjer Markdown-formatering. Anvand **fetstil**, *kursiv*, # rubriker, - listor, etc.
                            </p>
                        </div>
                        <textarea
                            id="content"
                            wire:model="content"
                            rows="15"
                            class="block w-full px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none resize-none border-0 font-mono text-sm"
                            placeholder="Skriv artikelns innehall har. Du kan anvanda Markdown-formatering..."
                        ></textarea>
                    </div>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="showList"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors text-sm font-medium"
                    >
                        Avbryt
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                    >
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">
                            {{ $view === 'create' ? 'Skapa artikel' : 'Spara andringar' }}
                        </span>
                        <span wire:loading wire:target="save">Sparar...</span>
                    </button>
                </div>
            </form>
        </div>

    @elseif($view === 'show' && $previewArticle)
        {{-- Article Preview --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    {{-- Article Header --}}
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        {{ $previewArticle->category }}
                                    </span>
                                    @php
                                        $statusColors = [
                                            'published' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                            'draft' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                            'archived' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                        ];
                                        $statusLabels = [
                                            'published' => 'Publicerad',
                                            'draft' => 'Utkast',
                                            'archived' => 'Arkiverad',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$previewArticle->status] ?? $statusColors['draft'] }}">
                                        {{ $statusLabels[$previewArticle->status] ?? $previewArticle->status }}
                                    </span>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $previewArticle->title }}
                                </h1>
                                @if($previewArticle->meta_description)
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                                        {{ $previewArticle->meta_description }}
                                    </p>
                                @endif
                            </div>
                            <button
                                wire:click="showEdit({{ $previewArticle->id }})"
                                class="ml-4 p-2 text-gray-400 hover:text-blue-600 dark:text-gray-500 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                title="Redigera"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Meta Information --}}
                        <div class="flex items-center gap-4 mt-4 text-sm text-gray-500 dark:text-gray-400">
                            @if($previewArticle->creator)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $previewArticle->creator->name }}
                                </span>
                            @endif
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $previewArticle->created_at->format('d M Y') }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ number_format($previewArticle->view_count) }} visningar
                            </span>
                        </div>

                        {{-- Tags --}}
                        @if($previewArticle->tags && count($previewArticle->tags) > 0)
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach($previewArticle->tags as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Article Content --}}
                    <div class="p-6">
                        <div class="prose prose-gray dark:prose-invert max-w-none">
                            {!! nl2br(e($previewArticle->content)) !!}
                        </div>
                    </div>

                    {{-- Voting Section --}}
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                                Var denna artikel hjalpsam?
                            </p>
                            <div class="flex items-center justify-center gap-3">
                                <button
                                    wire:click="voteHelpful({{ $previewArticle->id }})"
                                    class="inline-flex items-center px-4 py-2 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 bg-white dark:bg-gray-800 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors text-sm font-medium"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                    </svg>
                                    Ja ({{ $previewArticle->helpful_votes }})
                                </button>
                                <button
                                    wire:click="voteUnhelpful({{ $previewArticle->id }})"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors text-sm font-medium"
                                >
                                    <svg class="w-5 h-5 mr-2 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                    </svg>
                                    Nej ({{ $previewArticle->unhelpful_votes }})
                                </button>
                            </div>
                            @if($previewArticle->total_votes > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
                                    {{ $previewArticle->helpfulness_ratio }}% tyckte denna artikel var hjalpsam
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Article Info Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Artikeldetaljer</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Skapad</dt>
                            <dd class="text-gray-900 dark:text-white mt-1">{{ $previewArticle->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Uppdaterad</dt>
                            <dd class="text-gray-900 dark:text-white mt-1">{{ $previewArticle->updated_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">URL-slug</dt>
                            <dd class="text-gray-900 dark:text-white mt-1 font-mono text-xs break-all">{{ $previewArticle->slug }}</dd>
                        </div>
                        @if($previewArticle->ai_relevance_score)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">AI-relevans</dt>
                                <dd class="text-gray-900 dark:text-white mt-1">{{ number_format($previewArticle->ai_relevance_score * 100, 0) }}%</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Snabbatgarder</h3>
                    <div class="space-y-2">
                        <button
                            wire:click="showEdit({{ $previewArticle->id }})"
                            class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Redigera
                        </button>
                        <button
                            wire:click="duplicateArticle({{ $previewArticle->id }})"
                            class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Duplicera
                        </button>
                        @if($previewArticle->status !== 'published')
                            <button
                                wire:click="toggleStatus({{ $previewArticle->id }}, 'published')"
                                class="w-full flex items-center px-3 py-2 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Publicera
                            </button>
                        @endif
                        @if($previewArticle->status !== 'archived')
                            <button
                                wire:click="toggleStatus({{ $previewArticle->id }}, 'archived')"
                                class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                                Arkivera
                            </button>
                        @endif
                        <button
                            wire:click="confirmDelete({{ $previewArticle->id }})"
                            class="w-full flex items-center px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Radera
                        </button>
                    </div>
                </div>

                {{-- AI Keywords --}}
                @if($previewArticle->ai_keywords && count($previewArticle->ai_keywords) > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">AI-genererade nyckelord</h3>
                        <div class="flex flex-wrap gap-1">
                            @foreach($previewArticle->ai_keywords as $keyword)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                    {{ $keyword }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>

                {{-- Center modal --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Radera artikel
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Ar du saker pa att du vill radera denna artikel? Atgarden kan inte angras och all data kommer att forsvinna permanent.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <button
                            type="button"
                            wire:click="deleteArticle"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm"
                        >
                            Radera
                        </button>
                        <button
                            type="button"
                            wire:click="cancelDelete"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Avbryt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
