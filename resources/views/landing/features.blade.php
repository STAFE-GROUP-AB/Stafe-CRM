@extends('landing.layout')

@section('title', 'Features - Stafe CRM')
@section('description', 'Discover all the powerful features of Stafe CRM including AI-powered insights, workflow automation, revenue intelligence, and comprehensive customer management.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-purple-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6" data-aos="fade-up">
            Powerful Features for Modern Teams
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            Everything you need to manage customer relationships, drive sales, and grow your business with advanced AI-powered insights.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
            <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors">
                Start Free Trial
            </a>
            <a href="{{ route('landing.contact') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold border border-blue-600 hover:bg-blue-50 transition-colors">
                Schedule Demo
            </a>
        </div>
    </div>
</section>

<!-- Core CRM Features -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">
                Core CRM Functionality
            </h2>
            <p class="text-xl text-gray-600" data-aos="fade-up" data-aos-delay="200">
                Complete customer relationship management with all the essentials
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Company Management</h3>
                <p class="text-gray-600">Complete company profiles with contact information, industry classification, and revenue tracking with multi-currency support.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Contact Management</h3>
                <p class="text-gray-600">Individual contact profiles with full details, company associations, social media integration, and custom fields.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Deal Pipeline</h3>
                <p class="text-gray-600">Customizable pipeline stages with probability tracking, deal value forecasting, and expected close date management.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Task Management</h3>
                <p class="text-gray-600">Task creation with multiple types, priority levels, due date management, and assignment to team members.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes & Documentation</h3>
                <p class="text-gray-600">Contextual notes for any entity, private and public visibility, pinned important notes, and file attachments.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Flexible Tagging</h3>
                <p class="text-gray-600">Tag any entity with color-coded organization, powerful filtering, and smart search capabilities.</p>
            </div>
        </div>
    </div>
</section>

<!-- AI Features -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                AI-Powered Intelligence
            </h2>
            <p class="text-xl text-gray-600">
                Leverage artificial intelligence to boost your sales performance
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Smart Lead Scoring</h3>
                <p class="text-lg text-gray-600 mb-6">
                    Advanced AI algorithms analyze lead behavior, demographics, and engagement patterns to automatically score and prioritize prospects.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Behavioral analysis and scoring</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Predictive lead qualification</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Automated priority assignment</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-white p-8 rounded-xl shadow-lg" data-aos="fade-left">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Lead Score Dashboard</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                            <span class="text-sm text-gray-700">Sarah Johnson</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 95%"></div>
                            </div>
                            <span class="text-sm font-semibold text-green-600">95</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 animate-pulse"></div>
                            <span class="text-sm text-gray-700">Mike Chen</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 87%"></div>
                            </div>
                            <span class="text-sm font-semibold text-blue-600">87</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg transform transition-all duration-300 hover:scale-105 hover:shadow-md">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3 animate-pulse"></div>
                            <span class="text-sm text-gray-700">Emma Wilson</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 72%"></div>
                            </div>
                            <span class="text-sm font-semibold text-yellow-600">72</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Communication Features -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Unified Communications Platform
            </h2>
            <p class="text-xl text-gray-600">
                All your customer communications in one place
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Integration</h3>
                <p class="text-gray-600">Send, receive, and track emails with smart templates and automation.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">SMS & WhatsApp</h3>
                <p class="text-gray-600">Multi-channel messaging with automated routing and responses.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Voice & Video</h3>
                <p class="text-gray-600">Integrated VoIP calling with HD video and call recording.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v3M7 4H5a1 1 0 00-1 1v3m0 0v8a1 1 0 001 1h3M7 4h10M5 8h14M5 12h14M5 16h14"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Social Media</h3>
                <p class="text-gray-600">Monitor brand mentions and engage across all social platforms.</p>
            </div>
        </div>
    </div>
</section>

<!-- Workflow Automation & Integration -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">
                Workflow Automation & Integrations
            </h2>
            <p class="text-xl text-gray-600" data-aos="fade-up" data-aos-delay="200">
                Automate repetitive tasks and connect with your favorite tools
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
            <div data-aos="fade-right">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Smart Workflow Automation</h3>
                <p class="text-lg text-gray-600 mb-6">
                    Create intelligent workflows that automatically handle routine tasks, update records, send notifications, and move deals through your pipeline.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Trigger-based automation</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Email sequence automation</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Task auto-assignment</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Pipeline stage automation</span>
                    </li>
                </ul>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-xl" data-aos="fade-left">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Workflow Builder</h4>
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white text-sm font-semibold">1</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">New Lead Created</p>
                            <p class="text-xs text-gray-500">Trigger</p>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-0.5 h-6 bg-gray-300"></div>
                    </div>
                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                        <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white text-sm font-semibold">2</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Send Welcome Email</p>
                            <p class="text-xs text-gray-500">Action</p>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="w-0.5 h-6 bg-gray-300"></div>
                    </div>
                    <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white text-sm font-semibold">3</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Assign to Sales Rep</p>
                            <p class="text-xs text-gray-500">Action</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Integration Logos -->
        <div class="text-center mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-8" data-aos="fade-up">
                Seamlessly Integrate with 100+ Tools
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center opacity-70">
                <div class="flex items-center justify-center h-12" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-20 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">Slack</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-12" data-aos="zoom-in" data-aos-delay="200">
                    <div class="w-20 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">Gmail</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-12" data-aos="zoom-in" data-aos-delay="300">
                    <div class="w-20 h-8 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">Zoom</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-12" data-aos="zoom-in" data-aos-delay="400">
                    <div class="w-20 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">Teams</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-12" data-aos="zoom-in" data-aos-delay="500">
                    <div class="w-20 h-8 bg-gradient-to-r from-pink-500 to-red-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">HubSpot</span>
                    </div>
                </div>
                <div class="flex items-center justify-center h-12" data-aos="zoom-in" data-aos-delay="600">
                    <div class="w-20 h-8 bg-gradient-to-r from-teal-500 to-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold">Zapier</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Performance & Analytics -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4" data-aos="fade-up">
                Advanced Analytics & Reporting
            </h2>
            <p class="text-xl text-gray-600" data-aos="fade-up" data-aos-delay="200">
                Make data-driven decisions with powerful insights and visual analytics
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Custom Dashboards</h3>
                <p class="text-gray-600">Build personalized dashboards with drag-and-drop widgets to track the metrics that matter most to your business.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Sales Forecasting</h3>
                <p class="text-gray-600">Predict future revenue with AI-powered forecasting models that analyze historical data and current pipeline.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Performance Tracking</h3>
                <p class="text-gray-600">Monitor team performance with detailed metrics on conversion rates, activity levels, and goal achievement.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="400">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Visual Analytics</h3>
                <p class="text-gray-600">Interactive charts, heat maps, and relationship networks that make complex data easy to understand.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="500">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Automated Reports</h3>
                <p class="text-gray-600">Schedule and automate report generation and delivery to stakeholders with customizable formats.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow" data-aos="fade-up" data-aos-delay="600">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">ROI Analysis</h3>
                <p class="text-gray-600">Track return on investment across campaigns, channels, and activities to optimize your marketing spend.</p>
            </div>
        </div>
    </div>
</section>

<!-- Security Features -->
<section class="py-20 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-4" data-aos="fade-up">
                Enterprise Security & Compliance
            </h2>
            <p class="text-xl text-gray-300" data-aos="fade-up" data-aos-delay="200">
                Your data is protected with enterprise-grade security
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-800 p-6 rounded-xl hover:bg-gray-700 transition-colors" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">GDPR Compliance</h3>
                <p class="text-gray-300">Complete data privacy management with consent tracking and automated compliance workflows.</p>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-xl hover:bg-gray-700 transition-colors" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Data Encryption</h3>
                <p class="text-gray-300">Field-level encryption for sensitive data with configurable sensitivity levels.</p>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-xl hover:bg-gray-700 transition-colors" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Audit Trails</h3>
                <p class="text-gray-300">Comprehensive security event logging with risk categorization and compliance reporting.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6" data-aos="fade-up">
            Experience All Features Today
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            Start your free trial and discover how Stafe CRM can transform your business operations.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
            <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors transform hover:scale-105">
                Start Free Trial
            </a>
            <a href="{{ route('landing.contact') }}" class="bg-transparent text-white px-8 py-4 rounded-lg text-lg font-semibold border border-white hover:bg-white hover:text-blue-600 transition-colors transform hover:scale-105">
                Schedule Demo
            </a>
        </div>
    </div>
</section>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    once: true,
    offset: 100
  });
</script>
@endsection