<div class="p-6 lg:p-8 bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center space-x-4">
        <x-application-logo class="block h-16 w-auto" />
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent dark:from-green-400 dark:to-blue-400">
                Welcome to Stafe CRM
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                The world's best open-source CRM for 2026
            </p>
        </div>
    </div>

    <p class="mt-6 text-gray-700 dark:text-gray-300 leading-relaxed max-w-4xl">
        Built with the Laravel TALL Stack (Tailwind CSS, Alpine.js, Livewire, Laravel), Stafe CRM combines simplicity with power. 
        Manage your contacts, companies, deals, and tasks with AI-powered intelligence, multi-tenancy support, and enterprise-grade security.
    </p>
</div>

<div class="bg-gray-50 dark:bg-gray-800 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8">
    <!-- Core CRM -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-100 dark:bg-green-900/30">
                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h2 class="ms-4 text-xl font-semibold text-gray-900 dark:text-white">
                Core CRM
            </h2>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            Comprehensive contact, company, and deal management with custom fields, tagging, and powerful search capabilities.
        </p>
        <p class="mt-4 text-sm">
            <a href="{{ route('contacts.index') }}" class="inline-flex items-center font-semibold text-green-600 dark:text-green-400 hover:text-green-700">
                Manage Contacts
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-current">
                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>

    <!-- AI Intelligence -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <h2 class="ms-4 text-xl font-semibold text-gray-900 dark:text-white">
                AI Intelligence
            </h2>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            Smart lead scoring, predictive analytics, conversation intelligence, and automated data enrichment powered by AI.
        </p>
        <p class="mt-4 text-sm">
            <a href="{{ route('ai.configuration') }}" class="inline-flex items-center font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700">
                Explore AI Features
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-current">
                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>

    <!-- Multi-Tenancy -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h2 class="ms-4 text-xl font-semibold text-gray-900 dark:text-white">
                Teams & Tenants
            </h2>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            Complete data isolation between organizations with team management, custom domains, and role-based permissions.
        </p>
        <p class="mt-4 text-sm">
            <a href="{{ route('teams.show', auth()->user()->currentTeam) }}" class="inline-flex items-center font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700">
                Manage Teams
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-current">
                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>

    <!-- Security & Compliance -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-red-100 dark:bg-red-900/30">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="ms-4 text-xl font-semibold text-gray-900 dark:text-white">
                Enterprise Security
            </h2>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            GDPR compliance, field-level encryption, SSO, two-factor authentication, and comprehensive audit trails.
        </p>
        <p class="mt-4 text-sm">
            <a href="{{ route('profile.show') }}" class="inline-flex items-center font-semibold text-red-600 dark:text-red-400 hover:text-red-700">
                Security Settings
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-current">
                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>

    <!-- Analytics & Reports -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h2 class="ms-4 text-xl font-semibold text-gray-900 dark:text-white">
                Analytics & Insights
            </h2>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            Interactive dashboards, heat map analytics, pipeline visualization, and custom reporting with real-time data.
        </p>
        <p class="mt-4 text-sm">
            <a href="{{ route('revenue-intelligence.dashboard') }}" class="inline-flex items-center font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700">
                View Analytics
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-current">
                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                </svg>
            </a>
        </p>
    </div>

    <!-- Automation -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h2 class="ms-4 text-xl font-semibold text-gray-900 dark:text-white">
                Workflow Automation
            </h2>
        </div>
        <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
            Visual workflow builder, smart triggers, cadence automation, and A/B testing for maximum efficiency.
        </p>
        <p class="mt-4 text-sm">
            <span class="inline-flex items-center font-semibold text-orange-600 dark:text-orange-400">
                Coming Soon
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-current">
                    <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                </svg>
            </span>
        </p>
    </div>
</div>

    <div>
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            <h2 class="ms-3 text-xl font-semibold text-gray-900 dark:text-white">
                Authentication
            </h2>
        </div>

        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
            Authentication and registration views are included with Laravel Jetstream, as well as support for user email verification and resetting forgotten passwords. So, you're free to get started with what matters most: building your application.
        </p>
    </div>
</div>
