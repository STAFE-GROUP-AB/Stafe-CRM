<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\SystemSettings;
use App\Livewire\AiConfiguration;
use App\Livewire\LeadScoringDashboard;
use App\Livewire\CommunicationHub;
use App\Livewire\LiveChat;
use App\Http\Controllers\AiDemoController;
use App\Http\Controllers\TwilioWebhookController;
use App\Http\Controllers\ChatWidgetController;
use App\Http\Controllers\RevenueIntelligenceController;
use App\Http\Controllers\SalesEnablementController;
use App\Http\Controllers\Analytics\AnalyticsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register.post');

// Landing Pages (Public)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/features', [LandingController::class, 'features'])->name('landing.features');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('landing.pricing');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');

// Dashboard (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/settings', SystemSettings::class)->name('settings');
    
    // AI Features Routes
    Route::get('/ai/configuration', AiConfiguration::class)->name('ai.configuration');
    Route::get('/ai/lead-scoring', LeadScoringDashboard::class)->name('ai.lead-scoring');
    Route::get('/ai/keys', \App\Livewire\UserAiKeyManager::class)->name('ai.keys');
    
    // Stalled Customers Feature
    Route::get('/stalled-customers', \App\Livewire\StalledCustomers::class)->name('stalled-customers');
    
    // Companies - RESTful Routes
    Route::get('/companies', \App\Livewire\CompanyManager::class)->name('companies.index');
    Route::get('/companies/create', \App\Livewire\CompanyManager::class)->name('companies.create')->defaults('action', 'create');
    Route::get('/companies/{company}', \App\Livewire\CompanyManager::class)->name('companies.show')->defaults('action', 'show');
    Route::get('/companies/{company}/edit', \App\Livewire\CompanyManager::class)->name('companies.edit')->defaults('action', 'edit');
    
    // Contacts - RESTful Routes
    Route::get('/contacts', \App\Livewire\ContactManager::class)->name('contacts.index');
    Route::get('/contacts/create', \App\Livewire\ContactManager::class)->name('contacts.create')->defaults('action', 'create');
    Route::get('/contacts/{contact}', \App\Livewire\ContactManager::class)->name('contacts.show')->defaults('action', 'show');
    Route::get('/contacts/{contact}/edit', \App\Livewire\ContactManager::class)->name('contacts.edit')->defaults('action', 'edit');
    
    // Deals - RESTful Routes
    Route::get('/deals', \App\Livewire\DealManager::class)->name('deals.index');
    Route::get('/deals/create', \App\Livewire\DealManager::class)->name('deals.create')->defaults('action', 'create');
    Route::get('/deals/{deal}', \App\Livewire\DealManager::class)->name('deals.show')->defaults('action', 'show');
    Route::get('/deals/{deal}/edit', \App\Livewire\DealManager::class)->name('deals.edit')->defaults('action', 'edit');
    
    // Tasks - RESTful Routes
    Route::get('/tasks', \App\Livewire\TaskManager::class)->name('tasks.index');
    Route::get('/tasks/create', \App\Livewire\TaskManager::class)->name('tasks.create')->defaults('action', 'create');
    Route::get('/tasks/{task}', \App\Livewire\TaskManager::class)->name('tasks.show')->defaults('action', 'show');
    Route::get('/tasks/{task}/edit', \App\Livewire\TaskManager::class)->name('tasks.edit')->defaults('action', 'edit');

    // Communication Hub Routes
    Route::get('/communications', CommunicationHub::class)->name('communications.index');
    Route::get('/communications/chat/{sessionId?}', LiveChat::class)->name('communications.chat');
});

// Chat Widget Routes (public)
Route::get('/chat/widget', [ChatWidgetController::class, 'widget'])->name('chat.widget');
Route::get('/chat/embed.js', [ChatWidgetController::class, 'embedScript'])->name('chat.embed-script');

// Sales Enablement Suite Routes
Route::prefix('sales-enablement')->name('sales-enablement.')->middleware('auth')->group(function () {
    Route::get('/quotes', \App\Livewire\QuoteBuilder::class)->name('quotes');
    Route::get('/content-library', \App\Livewire\SalesContentLibrary::class)->name('content-library');
    Route::get('/battle-cards', \App\Livewire\BattleCardsManager::class)->name('battle-cards');
    Route::get('/playbooks', \App\Livewire\SalesPlaybooksManager::class)->name('playbooks');
    Route::get('/gamification', \App\Livewire\GamificationDashboard::class)->name('gamification');
    
    // Quote-specific routes
    Route::get('/quotes/{quote}/preview', [SalesEnablementController::class, 'previewQuote'])->name('quotes.preview');
    Route::get('/quotes/{quote}/pdf', [SalesEnablementController::class, 'downloadQuotePdf'])->name('quotes.pdf');
    Route::get('/quotes/{quote}/sign', [SalesEnablementController::class, 'signQuote'])->name('quotes.sign');
    Route::post('/quotes/{quote}/signature', [SalesEnablementController::class, 'saveSignature'])->name('quotes.signature');
    
    // Content-specific routes
    Route::get('/content/{content}/download', [SalesEnablementController::class, 'downloadContent'])->name('content.download');
    Route::get('/content/{content}/preview', [SalesEnablementController::class, 'previewContent'])->name('content.preview');
    
    // Playbook execution
    Route::get('/playbooks/{execution}/execute', [SalesEnablementController::class, 'executePlaybook'])->name('playbook-execution');
    Route::post('/playbooks/{execution}/complete-step', [SalesEnablementController::class, 'completePlaybookStep'])->name('playbook-step.complete');
});

// Customer Experience Platform Routes
Route::prefix('customer-experience')->name('customer-experience.')->middleware('auth')->group(function () {
    Route::get('/dashboard', \App\Livewire\CustomerExperienceDashboard::class)->name('dashboard');
    Route::get('/tickets', \App\Livewire\CustomerPortal::class)->name('tickets');
    Route::get('/knowledge-base', \App\Livewire\KnowledgeBaseManager::class)->name('knowledge-base');
    Route::get('/surveys', \App\Livewire\SurveyManager::class)->name('surveys');
    Route::get('/health-scores', \App\Livewire\CustomerHealthDashboard::class)->name('health');
    Route::get('/journey-mapping', \App\Livewire\JourneyMappingDashboard::class)->name('journeys');
    Route::get('/loyalty-programs', \App\Livewire\LoyaltyProgramManager::class)->name('loyalty');
});

// Visual Intelligence & Analytics Routes (Phase 4.7)
Route::prefix('analytics')->name('analytics.')->middleware('auth')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/dashboards', [AnalyticsController::class, 'dashboards'])->name('dashboards');
    Route::get('/heat-maps', [AnalyticsController::class, 'heatMaps'])->name('heat-maps');
    Route::get('/charts', [AnalyticsController::class, 'charts'])->name('charts');
    Route::get('/relationships', [AnalyticsController::class, 'relationships'])->name('relationships');
    Route::get('/pipeline', [AnalyticsController::class, 'pipeline'])->name('pipeline');
    Route::get('/forecasting', [AnalyticsController::class, 'forecasting'])->name('forecasting');
    
    // API endpoints for data retrieval
    Route::get('/api/dashboards/{dashboard}/data', [AnalyticsController::class, 'getDashboardData'])->name('api.dashboard-data');
    Route::get('/api/charts/{chart}/data', [AnalyticsController::class, 'getChartData'])->name('api.chart-data');
    Route::get('/api/heat-maps/{heatMap}/data', [AnalyticsController::class, 'getHeatMapData'])->name('api.heatmap-data');
    Route::get('/api/relationships/{network}/data', [AnalyticsController::class, 'getRelationshipNetworkData'])->name('api.relationship-data');
    Route::get('/api/pipeline/{visualization}/data', [AnalyticsController::class, 'getPipelineVisualizationData'])->name('api.pipeline-data');
    Route::get('/api/forecasting/{simulation}/data', [AnalyticsController::class, 'getForecastSimulationData'])->name('api.forecast-data');
});

// Revenue Intelligence Routes
Route::prefix('revenue-intelligence')->name('revenue-intelligence.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [RevenueIntelligenceController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [RevenueIntelligenceController::class, 'analytics'])->name('analytics');
    
    // Deal Risk Analysis
    Route::post('/deals/{deal}/analyze-risk', [RevenueIntelligenceController::class, 'analyzeDealRisk'])->name('deals.analyze-risk');
    
    // Competitive Intelligence
    Route::post('/deals/{deal}/analyze-competition', [RevenueIntelligenceController::class, 'analyzeCompetition'])->name('deals.analyze-competition');
    
    // Price Optimization
    Route::post('/deals/{deal}/optimize-price', [RevenueIntelligenceController::class, 'optimizePrice'])->name('deals.optimize-price');
    
    // Bulk Analysis
    Route::post('/bulk-analysis', [RevenueIntelligenceController::class, 'bulkAnalysis'])->name('bulk-analysis');
    
    // Territory Performance
    Route::get('/territory-performance', [RevenueIntelligenceController::class, 'territoryPerformance'])->name('territory-performance');
    
    // Commission Tracking
    Route::get('/commission-tracking', [RevenueIntelligenceController::class, 'commissionTracking'])->name('commission-tracking');
    
    // Sales Coaching
    Route::get('/sales-coaching', [RevenueIntelligenceController::class, 'salesCoaching'])->name('sales-coaching');
    Route::patch('/sales-coaching/{coaching}/status', [RevenueIntelligenceController::class, 'updateCoachingStatus'])->name('coaching.update-status');
});

// AI Demo Routes (for demonstration without authentication)
Route::get('/ai/demo/configuration', [AiDemoController::class, 'aiConfiguration'])->name('ai.demo.configuration');
Route::get('/ai/demo/lead-scoring', [AiDemoController::class, 'leadScoring'])->name('ai.demo.lead-scoring');

// Twilio Webhook Routes (public, no auth required)
Route::prefix('webhooks/twilio')->name('twilio.')->group(function () {
    Route::post('/voice/incoming', [TwilioWebhookController::class, 'handleIncomingCall'])->name('call.incoming');
    Route::post('/voice/status', [TwilioWebhookController::class, 'handleCallStatus'])->name('call.status');
    Route::post('/voice/twiml', [TwilioWebhookController::class, 'generateCallTwiml'])->name('call.twiml');
    Route::post('/sms/incoming', [TwilioWebhookController::class, 'handleIncomingSms'])->name('sms.incoming');
    Route::post('/sms/status', [TwilioWebhookController::class, 'handleSmsStatus'])->name('sms.status');
    Route::post('/whatsapp/incoming', [TwilioWebhookController::class, 'handleIncomingWhatsapp'])->name('whatsapp.incoming');
    Route::post('/whatsapp/status', [TwilioWebhookController::class, 'handleWhatsappStatus'])->name('whatsapp.status');
    Route::post('/recording/status', [TwilioWebhookController::class, 'handleRecordingComplete'])->name('recording.status');
});
