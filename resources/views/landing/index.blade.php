@extends('landing.layout')

@section('title', 'Stafe CRM - Professional CRM for Modern Teams')
@section('description', 'Transform your business with Stafe CRM - the most advanced CRM solution featuring AI-powered sales intelligence, workflow automation, and world-class customer management.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-purple-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                The Most Advanced
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                    CRM Platform
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Transform your business with AI-powered sales intelligence, workflow automation, and world-class customer management. Built for modern teams who demand excellence.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors">
                    Start Free Trial
                </a>
                <a href="{{ route('landing.features') }}" class="bg-white text-gray-900 px-8 py-4 rounded-lg text-lg font-semibold border border-gray-300 hover:bg-gray-50 transition-colors">
                    Explore Features
                </a>
            </div>
            <p class="text-gray-500 mt-4">
                30-day free trial • No credit card required • 999 SEK/user/year
            </p>
        </div>
    </div>
</section>

<!-- Features Overview -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything Your Business Needs
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                From core CRM functionality to advanced AI features, Stafe CRM provides all the tools you need to grow your business.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Core CRM -->
            <div class="text-center p-8 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Core CRM</h3>
                <p class="text-gray-600">
                    Complete contact, company, and deal management with customizable pipelines, tasks, and notes.
                </p>
            </div>
            
            <!-- AI Intelligence -->
            <div class="text-center p-8 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-purple-600 rounded-lg flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">AI Intelligence</h3>
                <p class="text-gray-600">
                    Smart lead scoring, predictive analytics, and AI-powered insights to boost your sales performance.
                </p>
            </div>
            
            <!-- Automation -->
            <div class="text-center p-8 rounded-xl bg-gradient-to-br from-green-50 to-green-100 hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-green-600 rounded-lg flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Automation</h3>
                <p class="text-gray-600">
                    Powerful workflow automation to streamline your processes and eliminate repetitive tasks.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Feature Highlights -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Revenue Intelligence -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">
                    Revenue Intelligence Engine
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    Make data-driven decisions with advanced analytics, deal risk assessment, and predictive forecasting that helps you close more deals.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-700">Deal risk analysis with AI-powered insights</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-700">Competitive intelligence tracking</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-700">Dynamic pricing optimization</span>
                    </li>
                </ul>
            </div>
            <div class="relative">
                <div class="bg-white rounded-xl shadow-2xl p-8">
                    <div class="mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">Deal Risk Assessment</h4>
                        <p class="text-gray-600">Acme Corp - Q1 Enterprise Deal</p>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Risk Level</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Medium</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Probability</span>
                            <span class="text-sm font-semibold text-gray-900">75%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Expected Close</span>
                            <span class="text-sm font-semibold text-gray-900">15 days</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Communications Hub -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="order-2 lg:order-1">
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Unified Communications</h4>
                            <p class="text-gray-600">All channels in one place</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Email: 5 new messages</span>
                        </div>
                        <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">SMS: 2 new messages</span>
                        </div>
                        <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">WhatsApp: 1 new message</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-1 lg:order-2">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">
                    Unified Communications Platform
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    Manage all your customer communications from one central hub. Email, SMS, WhatsApp, social media, and more.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-700">Integrated voice & video calling</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-700">AI call transcription & analysis</span>
                    </li>
                    <li class="flex items-start">
                        <div class="w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-700">Social media monitoring & engagement</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Preview -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Simple, Transparent Pricing
        </h2>
        <p class="text-xl text-gray-600 mb-12">
            999 SEK per user per year. No hidden fees, no complex tiers.
        </p>
        
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-xl shadow-lg p-8 border-2 border-blue-600">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Professional</h3>
                    <p class="text-gray-600">Everything you need to grow</p>
                </div>
                <div class="text-center mb-6">
                    <span class="text-5xl font-bold text-gray-900">999</span>
                    <span class="text-xl text-gray-600">SEK</span>
                    <div class="text-gray-600">per user per year</div>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">All CRM features</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">AI-powered insights</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Workflow automation</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Priority support</span>
                    </li>
                </ul>
                <a href="{{ route('dashboard') }}" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-block">
                    Start Free Trial
                </a>
            </div>
        </div>
        
        <p class="text-gray-500 mt-8">
            30-day free trial • Cancel anytime • Bring your own AI keys
        </p>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Transform Your Business?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Join thousands of teams who trust Stafe CRM to manage their customer relationships and drive growth.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                Start Free Trial
            </a>
            <a href="{{ route('landing.contact') }}" class="bg-transparent text-white px-8 py-4 rounded-lg text-lg font-semibold border border-white hover:bg-white hover:text-blue-600 transition-colors">
                Contact Sales
            </a>
        </div>
    </div>
</section>
@endsection