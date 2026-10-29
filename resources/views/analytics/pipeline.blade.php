@extends('layouts.app')

@section('title', 'Pipeline Visualization')

@section('content')
<div class="space-y-6" x-data="pipelineSankey()" x-init="init()">
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pipeline Visualization</h1>
                <p class="text-gray-600 dark:text-gray-400">Sankey diagrams, conversion funnel analysis, and multi-dimensional pipeline views</p>
            </div>
            <div class="flex space-x-3">
                <select x-model="dataMode" @change="loadData()" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    <option value="value">By Deal Value</option>
                    <option value="count">By Deal Count</option>
                </select>
                <button @click="loadData()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date From -->
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                <input type="date" id="dateFrom" x-model="filters.dateFrom" @change="loadData()"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>

            <!-- Date To -->
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                <input type="date" id="dateTo" x-model="filters.dateTo" @change="loadData()"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>

            <!-- Owner Filter -->
            <div>
                <label for="owner" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deal Owner</label>
                <select id="owner" x-model="filters.ownerId" @change="loadData()"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                    <option value="">All Owners</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Minimum Value -->
            <div>
                <label for="minValue" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Minimum Deal Value</label>
                <input type="number" id="minValue" x-model="filters.minValue" @change="loadData()" min="0" step="1000" placeholder="0"
                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
            </div>
        </div>
    </div>

    <!-- Pipeline Visualization -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pipeline Flow</h3>
            <div class="flex items-center space-x-4 text-sm">
                <span class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                    <span class="text-gray-600 dark:text-gray-400">In Progress</span>
                </span>
                <span class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                    <span class="text-gray-600 dark:text-gray-400">Won</span>
                </span>
                <span class="flex items-center">
                    <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                    <span class="text-gray-600 dark:text-gray-400">Lost</span>
                </span>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex items-center justify-center py-12">
            <svg class="animate-spin h-8 w-8 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-3 text-gray-600 dark:text-gray-400">Loading pipeline data...</span>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && (!sankeyData.nodes || sankeyData.nodes.length === 0)" class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Pipeline Data</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">There are no deals in the pipeline stages yet, or your filter criteria returned no results.</p>
        </div>

        <!-- Sankey Chart Container -->
        <div x-show="!loading && sankeyData.nodes && sankeyData.nodes.length > 0" class="relative">
            <div id="sankey-chart" class="w-full" style="min-height: 400px;"></div>
        </div>

        <!-- Tooltip -->
        <div id="sankey-tooltip" class="absolute hidden bg-gray-900 dark:bg-gray-700 text-white text-sm px-3 py-2 rounded-lg shadow-lg z-50 pointer-events-none">
            <div class="tooltip-content"></div>
        </div>
    </div>

    <!-- Pipeline Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Stage Statistics -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Stage Statistics</h3>
            <div class="space-y-4" x-show="!loading && sankeyData.stageStats">
                <template x-for="(stats, stageName) in sankeyData.stageStats" :key="stageName">
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="stageName"></span>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="stats.count"></span> deals
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="formatCurrency(stats.value)"></span>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="loading" class="animate-pulse space-y-3">
                <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
        </div>

        <!-- Pipeline Health -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pipeline Health</h3>
            <div class="space-y-4" x-show="!loading && sankeyData.stats">
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <div class="text-sm font-medium text-green-900 dark:text-green-300">Total Pipeline Value</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-400" x-text="formatCurrency(sankeyData.stats?.totalValue || 0)"></div>
                    <div class="text-sm text-green-600 dark:text-green-500">
                        <span x-text="sankeyData.stats?.totalDeals || 0"></span> total deals
                    </div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="text-sm font-medium text-blue-900 dark:text-blue-300">Average Deal Size</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-400" x-text="formatCurrency(sankeyData.stats?.averageDealValue || 0)"></div>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg">
                    <div class="text-sm font-medium text-amber-900 dark:text-amber-300">Conversion Rate</div>
                    <div class="text-2xl font-bold text-amber-700 dark:text-amber-400">
                        <span x-text="sankeyData.stats?.conversionRate || 0"></span>%
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg">
                        <div class="text-sm font-medium text-emerald-900 dark:text-emerald-300">Won Value</div>
                        <div class="text-xl font-bold text-emerald-700 dark:text-emerald-400" x-text="formatCurrency(sankeyData.stats?.wonValue || 0)"></div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                        <div class="text-sm font-medium text-red-900 dark:text-red-300">Lost Value</div>
                        <div class="text-xl font-bold text-red-700 dark:text-red-400" x-text="formatCurrency(sankeyData.stats?.lostValue || 0)"></div>
                    </div>
                </div>
            </div>
            <div x-show="loading" class="animate-pulse space-y-3">
                <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-sankey@0.12.3/dist/d3-sankey.min.js"></script>
<script>
    function pipelineSankey() {
        return {
            loading: false,
            dataMode: 'value',
            filters: {
                dateFrom: '',
                dateTo: '',
                ownerId: '',
                minValue: 0
            },
            sankeyData: {
                nodes: [],
                links: [],
                stats: {},
                stageStats: {}
            },

            init() {
                this.loadData();

                // Handle window resize
                window.addEventListener('resize', () => {
                    if (this.sankeyData.nodes && this.sankeyData.nodes.length > 0) {
                        this.renderSankey();
                    }
                });

                // Watch for dark mode changes
                const observer = new MutationObserver(() => {
                    if (this.sankeyData.nodes && this.sankeyData.nodes.length > 0) {
                        this.renderSankey();
                    }
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            },

            async loadData() {
                this.loading = true;

                try {
                    const params = new URLSearchParams();
                    if (this.filters.dateFrom) params.append('date_from', this.filters.dateFrom);
                    if (this.filters.dateTo) params.append('date_to', this.filters.dateTo);
                    if (this.filters.ownerId) params.append('owner_id', this.filters.ownerId);
                    if (this.filters.minValue > 0) params.append('min_value', this.filters.minValue);
                    params.append('data_mode', this.dataMode);

                    const response = await fetch(`{{ route('analytics.api.sankey-data') }}?${params.toString()}`);
                    const data = await response.json();

                    this.sankeyData = data;

                    if (data.nodes && data.nodes.length > 0) {
                        this.$nextTick(() => {
                            this.renderSankey();
                        });
                    }
                } catch (error) {
                    console.error('Error loading pipeline data:', error);
                } finally {
                    this.loading = false;
                }
            },

            renderSankey() {
                const container = document.getElementById('sankey-chart');
                if (!container) return;

                // Clear previous chart
                container.innerHTML = '';

                const isDarkMode = document.documentElement.classList.contains('dark');
                const textColor = isDarkMode ? '#e5e7eb' : '#374151';
                const linkOpacity = isDarkMode ? 0.4 : 0.5;

                // Set up dimensions
                const margin = { top: 20, right: 120, bottom: 20, left: 120 };
                const width = container.clientWidth - margin.left - margin.right;
                const height = 400 - margin.top - margin.bottom;

                // Create SVG
                const svg = d3.select(container)
                    .append('svg')
                    .attr('width', width + margin.left + margin.right)
                    .attr('height', height + margin.top + margin.bottom)
                    .append('g')
                    .attr('transform', `translate(${margin.left},${margin.top})`);

                // Create tooltip
                const tooltip = d3.select('#sankey-tooltip');

                // Create sankey generator
                const sankey = d3.sankey()
                    .nodeId(d => d.name)
                    .nodeWidth(20)
                    .nodePadding(15)
                    .extent([[0, 0], [width, height]]);

                // Prepare data
                const nodes = this.sankeyData.nodes.map(d => ({...d}));
                const links = this.sankeyData.links.map(d => ({
                    source: nodes[d.source].name,
                    target: nodes[d.target].name,
                    value: d.value,
                    deals: d.deals,
                    totalValue: d.totalValue
                }));

                // Generate sankey layout
                const graph = sankey({
                    nodes: nodes,
                    links: links
                });

                // Color scale for links
                const getNodeColor = (node) => {
                    if (node.type === 'won') return isDarkMode ? '#22c55e' : '#16a34a';
                    if (node.type === 'lost') return isDarkMode ? '#ef4444' : '#dc2626';
                    return isDarkMode ? '#3b82f6' : '#2563eb';
                };

                // Add links with gradient
                const defs = svg.append('defs');

                graph.links.forEach((link, i) => {
                    const gradient = defs.append('linearGradient')
                        .attr('id', `gradient-${i}`)
                        .attr('gradientUnits', 'userSpaceOnUse')
                        .attr('x1', link.source.x1)
                        .attr('x2', link.target.x0);

                    gradient.append('stop')
                        .attr('offset', '0%')
                        .attr('stop-color', getNodeColor(link.source));

                    gradient.append('stop')
                        .attr('offset', '100%')
                        .attr('stop-color', getNodeColor(link.target));
                });

                // Draw links
                const link = svg.append('g')
                    .attr('fill', 'none')
                    .selectAll('g')
                    .data(graph.links)
                    .join('g');

                link.append('path')
                    .attr('d', d3.sankeyLinkHorizontal())
                    .attr('stroke', (d, i) => `url(#gradient-${i})`)
                    .attr('stroke-width', d => Math.max(1, d.width))
                    .attr('stroke-opacity', linkOpacity)
                    .style('cursor', 'pointer')
                    .on('mouseover', function(event, d) {
                        d3.select(this)
                            .attr('stroke-opacity', isDarkMode ? 0.7 : 0.8);

                        const content = `
                            <div class="font-semibold">${d.source.name} -> ${d.target.name}</div>
                            <div>Deals: ${d.deals}</div>
                            <div>Value: ${formatCurrency(d.totalValue)}</div>
                        `;

                        tooltip.select('.tooltip-content').html(content);
                        tooltip
                            .style('left', (event.pageX + 10) + 'px')
                            .style('top', (event.pageY - 10) + 'px')
                            .classed('hidden', false);
                    })
                    .on('mouseout', function() {
                        d3.select(this)
                            .attr('stroke-opacity', linkOpacity);
                        tooltip.classed('hidden', true);
                    });

                // Draw nodes
                const node = svg.append('g')
                    .selectAll('g')
                    .data(graph.nodes)
                    .join('g');

                node.append('rect')
                    .attr('x', d => d.x0)
                    .attr('y', d => d.y0)
                    .attr('height', d => Math.max(1, d.y1 - d.y0))
                    .attr('width', d => d.x1 - d.x0)
                    .attr('fill', d => getNodeColor(d))
                    .attr('rx', 3)
                    .attr('ry', 3)
                    .style('cursor', 'pointer')
                    .on('mouseover', function(event, d) {
                        d3.select(this)
                            .attr('opacity', 0.8);

                        const stageStats = this.__data__;
                        const content = `
                            <div class="font-semibold">${d.name}</div>
                            <div>Current Deals: ${d.value || 0}</div>
                        `;

                        tooltip.select('.tooltip-content').html(content);
                        tooltip
                            .style('left', (event.pageX + 10) + 'px')
                            .style('top', (event.pageY - 10) + 'px')
                            .classed('hidden', false);
                    })
                    .on('mouseout', function() {
                        d3.select(this)
                            .attr('opacity', 1);
                        tooltip.classed('hidden', true);
                    });

                // Add node labels
                node.append('text')
                    .attr('x', d => d.x0 < width / 2 ? d.x0 - 6 : d.x1 + 6)
                    .attr('y', d => (d.y1 + d.y0) / 2)
                    .attr('dy', '0.35em')
                    .attr('text-anchor', d => d.x0 < width / 2 ? 'end' : 'start')
                    .attr('fill', textColor)
                    .attr('font-size', '12px')
                    .attr('font-weight', '500')
                    .text(d => d.name);

                function formatCurrency(value) {
                    return new Intl.NumberFormat('sv-SE', {
                        style: 'currency',
                        currency: 'SEK',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                }
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('sv-SE', {
                    style: 'currency',
                    currency: 'SEK',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }
        };
    }
</script>
@endpush
