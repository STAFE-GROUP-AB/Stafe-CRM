<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Stafe CRM - The Enterprise CRM without the Enterprise Lock-in. Open source, AI-powered, and fully featured.">

    <title>Stafe CRM - Enterprise CRM Without Lock-in</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-balance { text-wrap: balance; }
        .highlight {
            background: linear-gradient(180deg, transparent 50%, #fef08a 50%);
            padding: 0 4px;
            margin: 0 -4px;
        }
        .highlight-full {
            background-color: #fef08a;
            padding: 2px 8px;
            margin: 0 2px;
        }
        .marker {
            position: relative;
            display: inline;
        }
        .marker::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 40%;
            background-color: #fef08a;
            z-index: -1;
        }
    </style>
</head>
<body class="antialiased bg-amber-50 text-stone-900">

    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 bg-amber-50/95 backdrop-blur-sm border-b border-amber-200/50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-xl font-black text-stone-900 tracking-tight">
                    Stafe CRM
                </a>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-semibold text-stone-600 hover:text-stone-900">Features</a>
                    <a href="#pricing" class="text-sm font-semibold text-stone-600 hover:text-stone-900">Pricing</a>
                    <a href="https://github.com/STAFE-GROUP-AB/Stafe-CRM" target="_blank" class="text-sm font-semibold text-stone-600 hover:text-stone-900">GitHub</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-stone-600 hover:text-stone-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-stone-600 hover:text-stone-900">Sign in</a>
                        @endauth
                    @endif
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-stone-900 rounded-full hover:bg-stone-800 transition-all">
                        Try it free
                    </a>
                </div>

                <button type="button" id="mobile-menu-btn" class="md:hidden p-2 text-stone-600 hover:text-stone-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden border-t border-amber-200 bg-amber-50">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-sm font-semibold text-stone-600">Features</a>
                <a href="#pricing" class="block text-sm font-semibold text-stone-600">Pricing</a>
                <a href="https://github.com/STAFE-GROUP-AB/Stafe-CRM" target="_blank" class="block text-sm font-semibold text-stone-600">GitHub</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block text-sm font-semibold text-stone-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-sm font-semibold text-stone-600">Sign in</a>
                    @endauth
                @endif
                <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 text-sm font-bold text-white bg-stone-900 rounded-full">
                    Try it free
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="bg-amber-50 border-b border-amber-200/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center">
            <p class="inline-block text-sm font-black text-stone-800 uppercase tracking-widest mb-6 bg-yellow-300 px-4 py-1 rounded-full">
                100% Open Source CRM
            </p>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-stone-900 tracking-tight leading-[0.95] text-balance">
                The Enterprise CRM<br>
                <span class="highlight">without the Lock-in</span>
            </h1>
            <p class="mt-8 text-xl md:text-2xl text-stone-600 max-w-2xl mx-auto leading-relaxed font-medium">
                AI-powered. <span class="highlight">Host it yourself for free</span>, or let us run it for you.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 text-lg font-bold text-white bg-emerald-600 rounded-full hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/25">
                    Start your free trial
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="https://github.com/STAFE-GROUP-AB/Stafe-CRM" target="_blank" class="inline-flex items-center px-8 py-4 text-lg font-bold text-stone-700 hover:text-stone-900 transition-all">
                    <svg class="mr-2 w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                    </svg>
                    View on GitHub
                </a>
            </div>
            <p class="mt-6 text-sm text-stone-500 font-medium">
                No credit card required. Cancel anytime.
            </p>
        </div>
    </section>

    <!-- SOCIAL PROOF BAR -->
    <section class="bg-stone-900 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center items-center gap-x-8 gap-y-4 text-sm font-bold">
                <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded">Unlimited users</span>
                <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded">Unlimited contacts</span>
                <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded">Unlimited storage</span>
                <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded">All features included</span>
            </div>
        </div>
    </section>

    <!-- WHY SECTION -->
    <section class="bg-white border-b border-stone-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h2 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight leading-snug">
                You could spend hours on DevOps.<br>
                <span class="bg-yellow-300 px-2">Or you could close deals.</span>
            </h2>
            <p class="mt-6 text-lg text-stone-600 leading-relaxed">
                Stafe Cloud turns server provisioning, Nginx configs, and SMTP debugging into a <span class="font-bold text-stone-900 bg-yellow-200 px-1">30-second sign-up</span>. Same open source code, zero infrastructure headaches.
            </p>
        </div>
    </section>

    <!-- COMPARISON TABLE -->
    <section id="pricing" class="bg-amber-50 border-b border-amber-200/50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight">
                    Pick your path
                </h2>
                <p class="mt-4 text-lg text-stone-600">
                    Same powerful CRM. Your infrastructure choice.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Self-Hosted -->
                <div class="bg-white rounded-2xl p-8 border-2 border-stone-200">
                    <div class="text-sm font-bold text-stone-500 uppercase tracking-wider mb-2">Self-Hosted</div>
                    <div class="text-4xl font-black text-stone-900 mb-1">$0<span class="text-lg font-semibold text-stone-500">/mo</span></div>
                    <div class="text-stone-600 mb-6">Forever free, host it yourself</div>

                    <a href="https://github.com/STAFE-GROUP-AB/Stafe-CRM" target="_blank" class="block w-full text-center px-6 py-3 text-base font-bold text-stone-700 bg-stone-100 rounded-full hover:bg-stone-200 transition-all mb-8">
                        Download from GitHub
                    </a>

                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start"><svg class="w-5 h-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-stone-700">100% open source code</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-stone-700">All enterprise features</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-stone-300 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg><span class="text-stone-500">Manual updates (git pull)</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-stone-300 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg><span class="text-stone-500">You handle backups</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-stone-300 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg><span class="text-stone-500">Community support</span></li>
                    </ul>
                </div>

                <!-- Stafe Cloud -->
                <div class="bg-stone-900 rounded-2xl p-8 border-2 border-stone-900 relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-yellow-400 text-stone-900 text-xs font-black uppercase tracking-wider rounded-full">
                        Recommended
                    </div>
                    <div class="text-sm font-bold text-yellow-400 uppercase tracking-wider mb-2">Stafe Cloud</div>
                    <div class="text-4xl font-black text-white mb-1">$49<span class="text-lg font-semibold text-stone-400">/mo</span></div>
                    <div class="text-stone-400 mb-6">Flat rate. No per-seat pricing.</div>

                    <a href="{{ route('register') }}" class="block w-full text-center px-6 py-3 text-base font-black text-stone-900 bg-yellow-400 rounded-full hover:bg-yellow-300 transition-all mb-8">
                        Start 14-day free trial
                    </a>

                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start"><svg class="w-5 h-5 text-emerald-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-stone-300">100% open source code</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-emerald-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-stone-300">All enterprise features</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-white font-bold">Automatic updates</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-white font-bold">Daily automated backups</span></li>
                        <li class="flex items-start"><svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span class="text-white font-bold">Priority email support</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ANTI-COMPETITOR SECTION -->
    <section class="bg-stone-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight">
                Stop paying the<br>
                <span class="bg-yellow-400 text-stone-900 px-3">"contact tax"</span>
            </h2>
            <p class="mt-6 text-xl text-stone-400 max-w-2xl mx-auto">
                HubSpot and Salesforce charge you more as you grow. We don't.
            </p>

            <!-- Price Chart -->
            <div class="mt-12 bg-stone-800 rounded-2xl p-8 max-w-xl mx-auto">
                <div class="text-left text-sm font-bold text-stone-500 mb-4">Monthly cost as you grow</div>
                <div class="relative h-48">
                    <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-stone-500 font-bold pr-4">
                        <span>$2000</span>
                        <span>$1000</span>
                        <span>$500</span>
                        <span>$0</span>
                    </div>
                    <div class="ml-12 h-full relative border-l border-b border-stone-700">
                        <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                            <path d="M 0 98 Q 40 95 60 75 T 100 10" stroke="#ef4444" stroke-width="3" fill="none" stroke-linecap="round" opacity="0.8"/>
                            <path d="M 0 88 L 100 88" stroke="#facc15" stroke-width="4" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
                <div class="flex justify-center gap-8 mt-6 text-sm font-bold">
                    <div class="flex items-center"><span class="w-4 h-1 bg-yellow-400 rounded mr-2"></span><span class="text-stone-300">Stafe ($49 flat)</span></div>
                    <div class="flex items-center"><span class="w-4 h-1 bg-red-500 rounded mr-2"></span><span class="text-stone-400">Others</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURE GRID - ALL FEATURES -->
    <section id="features" class="bg-white border-b border-stone-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-stone-900 tracking-tight">
                    Everything you need to <span class="bg-yellow-300 px-2">close deals</span>
                </h2>
                <p class="mt-4 text-lg text-stone-600">Enterprise-grade features. No enterprise price tag.</p>
            </div>

            <!-- Core CRM -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">01</span>
                    Core CRM
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Company Management</h4>
                        <p class="text-stone-600 text-sm">Complete company profiles, industry classification, revenue tracking, and custom fields.</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Contact Management</h4>
                        <p class="text-stone-600 text-sm">Individual profiles, company relationships, social links, lifetime value, and tagging system.</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Deal Pipeline</h4>
                        <p class="text-stone-600 text-sm">Customizable stages, probability tracking, weighted forecasting, and multi-currency support.</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Task Management</h4>
                        <p class="text-stone-600 text-sm">Calls, emails, meetings with priority levels, due dates, and team assignments.</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Notes & Documentation</h4>
                        <p class="text-stone-600 text-sm">Contextual notes, private/public visibility, pinned notes, and file attachments.</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-6 border-2 border-amber-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Custom Fields</h4>
                        <p class="text-stone-600 text-sm">Add fields to any entity. Text, number, date, select types with validation rules.</p>
                    </div>
                </div>
            </div>

            <!-- AI Intelligence -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">02</span>
                    AI-Powered Intelligence
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-stone-900 rounded-xl p-6 text-white">
                        <div class="bg-yellow-400 text-stone-900 text-xs font-black px-2 py-1 rounded inline-block mb-3">AI</div>
                        <h4 class="text-lg font-black mb-2">Smart Lead Scoring</h4>
                        <p class="text-stone-400 text-sm">AI analyzes behavior, demographics, and engagement to prioritize prospects automatically.</p>
                    </div>
                    <div class="bg-stone-900 rounded-xl p-6 text-white">
                        <div class="bg-yellow-400 text-stone-900 text-xs font-black px-2 py-1 rounded inline-block mb-3">AI</div>
                        <h4 class="text-lg font-black mb-2">Predictive Forecasting</h4>
                        <p class="text-stone-400 text-sm">ML models provide revenue predictions with confidence intervals and risk assessment.</p>
                    </div>
                    <div class="bg-stone-900 rounded-xl p-6 text-white">
                        <div class="bg-yellow-400 text-stone-900 text-xs font-black px-2 py-1 rounded inline-block mb-3">AI</div>
                        <h4 class="text-lg font-black mb-2">Conversation Intelligence</h4>
                        <p class="text-stone-400 text-sm">AI-powered analysis of calls, emails, and meetings. Extract insights and next actions.</p>
                    </div>
                    <div class="bg-stone-900 rounded-xl p-6 text-white">
                        <div class="bg-yellow-400 text-stone-900 text-xs font-black px-2 py-1 rounded inline-block mb-3">AI</div>
                        <h4 class="text-lg font-black mb-2">Data Enrichment</h4>
                        <p class="text-stone-400 text-sm">Smart data entry that auto-completes contact and company info from multiple sources.</p>
                    </div>
                    <div class="bg-stone-900 rounded-xl p-6 text-white">
                        <div class="bg-yellow-400 text-stone-900 text-xs font-black px-2 py-1 rounded inline-block mb-3">AI</div>
                        <h4 class="text-lg font-black mb-2">Deal Risk Analysis</h4>
                        <p class="text-stone-400 text-sm">AI assesses deal health, identifies risk factors, and suggests intervention strategies.</p>
                    </div>
                    <div class="bg-stone-900 rounded-xl p-6 text-white">
                        <div class="bg-yellow-400 text-stone-900 text-xs font-black px-2 py-1 rounded inline-block mb-3">AI</div>
                        <h4 class="text-lg font-black mb-2">Price Optimization</h4>
                        <p class="text-stone-400 text-sm">AI-suggested pricing based on historical data, market conditions, and competitors.</p>
                    </div>
                </div>
            </div>

            <!-- Communications -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">03</span>
                    Omni-Channel Communications
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">VoIP Calling</h4>
                        <p class="text-stone-600 text-sm">Built-in via Twilio with call recording and transcription.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">SMS Messaging</h4>
                        <p class="text-stone-600 text-sm">Send and receive text messages directly from the CRM.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">WhatsApp</h4>
                        <p class="text-stone-600 text-sm">Unified inbox for WhatsApp with automated routing.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">Email Integration</h4>
                        <p class="text-stone-600 text-sm">Send, receive, track opens/clicks. Smart templates included.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">Live Chat</h4>
                        <p class="text-stone-600 text-sm">Embeddable chat widgets with AI-powered chatbots.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">Call Transcription</h4>
                        <p class="text-stone-600 text-sm">Real-time transcription with speaker ID and keywords.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">Social Monitoring</h4>
                        <p class="text-stone-600 text-sm">Track brand mentions across major social platforms.</p>
                    </div>
                    <div class="bg-sky-50 rounded-xl p-5 border-2 border-sky-100">
                        <h4 class="font-black text-stone-900 mb-1">Email Templates</h4>
                        <p class="text-stone-600 text-sm">Dynamic templates with variable substitution.</p>
                    </div>
                </div>
            </div>

            <!-- Automation -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">04</span>
                    Visual Automation
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Workflow Builder</h4>
                        <p class="text-stone-600 text-sm">Visual drag-and-drop builder for multi-step automation workflows.</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Smart Triggers</h4>
                        <p class="text-stone-600 text-sm">Event-based, scheduled, and manual triggers for any workflow.</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Lead Routing</h4>
                        <p class="text-stone-600 text-sm">AI-powered lead assignment based on rules and rep performance.</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Cadence Sequences</h4>
                        <p class="text-stone-600 text-sm">Multi-touch automated outreach with email, calls, and tasks.</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">A/B Testing</h4>
                        <p class="text-stone-600 text-sm">Built-in A/B testing engine for workflows and content.</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Dynamic Content</h4>
                        <p class="text-stone-600 text-sm">Personalized content based on contact behavior and data.</p>
                    </div>
                </div>
            </div>

            <!-- Sales Enablement -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">05</span>
                    Sales Enablement
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-violet-50 rounded-xl p-6 border-2 border-violet-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Quote Builder</h4>
                        <p class="text-stone-600 text-sm">Dynamic proposal generation with smart pricing and approval workflows.</p>
                    </div>
                    <div class="bg-violet-50 rounded-xl p-6 border-2 border-violet-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">E-Signatures</h4>
                        <p class="text-stone-600 text-sm">Built-in e-signature capture for quotes and contracts.</p>
                    </div>
                    <div class="bg-violet-50 rounded-xl p-6 border-2 border-violet-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Content Library</h4>
                        <p class="text-stone-600 text-sm">Centralized sales content with usage analytics and recommendations.</p>
                    </div>
                    <div class="bg-violet-50 rounded-xl p-6 border-2 border-violet-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Battle Cards</h4>
                        <p class="text-stone-600 text-sm">Competitive positioning with objection handling guides.</p>
                    </div>
                    <div class="bg-violet-50 rounded-xl p-6 border-2 border-violet-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Sales Playbooks</h4>
                        <p class="text-stone-600 text-sm">Interactive guided selling with contextual coaching.</p>
                    </div>
                    <div class="bg-violet-50 rounded-xl p-6 border-2 border-violet-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Gamification</h4>
                        <p class="text-stone-600 text-sm">Achievements, leaderboards, and performance competitions.</p>
                    </div>
                </div>
            </div>

            <!-- Customer Experience -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">06</span>
                    Customer Experience
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Customer Portal</h4>
                        <p class="text-stone-600 text-sm">Self-service portal with ticket management and communication history.</p>
                    </div>
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Knowledge Base</h4>
                        <p class="text-stone-600 text-sm">Searchable help center for customer self-service.</p>
                    </div>
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Surveys & NPS</h4>
                        <p class="text-stone-600 text-sm">NPS, CSAT tracking with custom surveys and sentiment analysis.</p>
                    </div>
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Health Scoring</h4>
                        <p class="text-stone-600 text-sm">Multi-factor customer health with automated intervention triggers.</p>
                    </div>
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Journey Mapping</h4>
                        <p class="text-stone-600 text-sm">Visual customer journey tracking with touchpoint optimization.</p>
                    </div>
                    <div class="bg-rose-50 rounded-xl p-6 border-2 border-rose-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Loyalty Programs</h4>
                        <p class="text-stone-600 text-sm">Points, tiers, referral tracking, and reward automation.</p>
                    </div>
                </div>
            </div>

            <!-- Security & Analytics -->
            <div class="mb-16">
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">07</span>
                    Security & Analytics
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">GDPR Compliance</h4>
                        <p class="text-stone-600 text-sm">Consent tracking, data requests, automated workflows.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">SSO Integration</h4>
                        <p class="text-stone-600 text-sm">Google, Microsoft, Okta, and custom SAML/OAuth.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">Field Encryption</h4>
                        <p class="text-stone-600 text-sm">Granular encryption for sensitive data fields.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">Audit Trails</h4>
                        <p class="text-stone-600 text-sm">Comprehensive security event logging and reporting.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">IP Whitelisting</h4>
                        <p class="text-stone-600 text-sm">Network-level access controls with CIDR support.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">Data Retention</h4>
                        <p class="text-stone-600 text-sm">Automated data lifecycle management policies.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">Role Permissions</h4>
                        <p class="text-stone-600 text-sm">35+ granular permissions with custom roles.</p>
                    </div>
                    <div class="bg-stone-100 rounded-xl p-5 border-2 border-stone-200">
                        <h4 class="font-black text-stone-900 mb-1">Multi-Tenancy</h4>
                        <p class="text-stone-600 text-sm">Complete data isolation between organizations.</p>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div>
                <h3 class="text-sm font-black text-stone-500 uppercase tracking-widest mb-8 flex items-center">
                    <span class="bg-yellow-400 text-stone-900 px-3 py-1 rounded mr-3">08</span>
                    Visual Intelligence
                </h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Interactive Dashboards</h4>
                        <p class="text-stone-600 text-sm">Drag-and-drop dashboard builder with real-time data visualization.</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Pipeline Visualization</h4>
                        <p class="text-stone-600 text-sm">Sankey diagrams, conversion funnels, and multi-dimensional views.</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Forecasting Simulator</h4>
                        <p class="text-stone-600 text-sm">What-if scenario modeling and trend analysis tools.</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Relationship Mapping</h4>
                        <p class="text-stone-600 text-sm">Visual network of customer relationships and stakeholders.</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Heat Map Analytics</h4>
                        <p class="text-stone-600 text-sm">Visual performance representation across dimensions.</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-6 border-2 border-orange-100">
                        <h4 class="text-lg font-black text-stone-900 mb-2">Custom Charts</h4>
                        <p class="text-stone-600 text-sm">Build custom charts with advanced filtering and drill-down.</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FINAL CTA -->
    <section class="bg-yellow-400">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight">
                Ready to escape the contact tax?
            </h2>
            <p class="mt-4 text-lg text-stone-700 font-medium">
                Start your free trial today. No credit card required.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center mt-8 px-8 py-4 text-lg font-black text-white bg-stone-900 rounded-full hover:bg-stone-800 transition-all">
                Start free trial
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-stone-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="text-xl font-black text-white">Stafe CRM</div>
                    <p class="mt-4 text-stone-400 text-sm leading-relaxed max-w-sm">
                        The open-source, AI-powered CRM for teams who refuse to pay the contact tax.
                    </p>
                    <p class="mt-6 text-stone-500 text-sm font-medium">
                        Proudly built with Laravel & Livewire
                    </p>
                </div>

                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-wider">Product</h4>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#features" class="text-sm text-stone-400 hover:text-white font-medium">Features</a></li>
                        <li><a href="#pricing" class="text-sm text-stone-400 hover:text-white font-medium">Pricing</a></li>
                        <li><a href="https://github.com/STAFE-GROUP-AB/Stafe-CRM" target="_blank" class="text-sm text-stone-400 hover:text-white font-medium">GitHub</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-wider">Company</h4>
                    <ul class="mt-4 space-y-3">
                        <li><a href="mailto:andreas@stafegroup.com" class="text-sm text-stone-400 hover:text-white font-medium">Contact</a></li>
                        <li><a href="https://github.com/STAFE-GROUP-AB/Stafe-CRM/issues" target="_blank" class="text-sm text-stone-400 hover:text-white font-medium">Support</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-stone-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-stone-500 text-sm font-medium">&copy; {{ date('Y') }} Stafe Group AB</p>
                <p class="text-stone-500 text-sm mt-4 md:mt-0 font-medium">Built in Sweden</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
