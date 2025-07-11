<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Stafe CRM - Professional CRM for Modern Teams')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('description', 'Transform your business with Stafe CRM - the most advanced CRM solution featuring AI-powered sales intelligence, workflow automation, and world-class customer management.')">
    <meta name="keywords" content="CRM, Customer Relationship Management, Sales Management, AI CRM, Sales Intelligence, Workflow Automation, Team Collaboration">
    <meta name="author" content="Stafe Group AB">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Stafe CRM - Professional CRM for Modern Teams')">
    <meta property="og:description" content="@yield('description', 'Transform your business with Stafe CRM - the most advanced CRM solution featuring AI-powered sales intelligence, workflow automation, and world-class customer management.')">
    <meta property="og:image" content="{{ asset('images/stafe-crm-og.jpg') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Stafe CRM - Professional CRM for Modern Teams')">
    <meta property="twitter:description" content="@yield('description', 'Transform your business with Stafe CRM - the most advanced CRM solution featuring AI-powered sales intelligence, workflow automation, and world-class customer management.')">
    <meta property="twitter:image" content="{{ asset('images/stafe-crm-og.jpg') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="bg-white font-inter antialiased">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('landing') }}" class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">S</span>
                        </div>
                        <span class="ml-3 text-xl font-bold text-gray-900">Stafe CRM</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('landing') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Home</a>
                    <a href="{{ route('landing.features') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Features</a>
                    <a href="{{ route('landing.pricing') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Pricing</a>
                    <a href="{{ route('landing.contact') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
                </div>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 transition-colors">Sign In</a>
                    <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Start Free Trial</a>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button x-data x-on:click="$dispatch('toggle-mobile-menu')" class="text-gray-700 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-data="{ open: false }" x-on:toggle-mobile-menu.window="open = !open" x-show="open" x-transition class="md:hidden bg-white border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('landing') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Home</a>
                <a href="{{ route('landing.features') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Features</a>
                <a href="{{ route('landing.pricing') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Pricing</a>
                <a href="{{ route('landing.contact') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
                <div class="pt-2 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 transition-colors">Sign In</a>
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">Start Free Trial</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">S</span>
                        </div>
                        <span class="ml-3 text-xl font-bold">Stafe CRM</span>
                    </div>
                    <p class="text-gray-400 mb-4 max-w-md">
                        Transform your business with the most advanced CRM solution featuring AI-powered sales intelligence, workflow automation, and world-class customer management.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <span class="sr-only">Twitter</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Product Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Product</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('landing.features') }}" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="{{ route('landing.pricing') }}" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
                
                <!-- Support Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="{{ route('landing.contact') }}" class="hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} Stafe Group AB. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">License</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>