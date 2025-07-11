@extends('landing.layout')

@section('title', 'Contact Us - Stafe CRM')
@section('description', 'Get in touch with our team for questions, support, or to schedule a demo of Stafe CRM.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 to-purple-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            Get in Touch
        </h1>
        <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
            Have questions about Stafe CRM? Our team is here to help you find the perfect solution for your business.
        </p>
    </div>
</section>

<!-- Contact Information -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Contact Form -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-8">
                    Send us a message
                </h2>
                
                <form class="space-y-6" x-data="{ submitted: false }" @submit.prevent="submitted = true">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name
                            </label>
                            <input type="text" id="first_name" name="first_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name
                            </label>
                            <input type="text" id="last_name" name="last_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                            Company
                        </label>
                        <input type="text" id="company" name="company"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject
                        </label>
                        <select id="subject" name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a subject</option>
                            <option value="demo">Schedule a Demo</option>
                            <option value="pricing">Pricing Questions</option>
                            <option value="support">Technical Support</option>
                            <option value="partnership">Partnership Inquiry</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message
                        </label>
                        <textarea id="message" name="message" rows="6" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Tell us about your requirements..."></textarea>
                    </div>
                    
                    <div x-show="!submitted">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            Send Message
                        </button>
                    </div>
                    
                    <div x-show="submitted" class="text-center p-6 bg-green-50 rounded-lg">
                        <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-green-900 mb-2">Message Sent!</h3>
                        <p class="text-green-700">Thank you for your message. We'll get back to you within 24 hours.</p>
                    </div>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-8">
                    Contact Information
                </h2>
                
                <div class="space-y-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                            <p class="text-gray-600">
                                <a href="mailto:sales@stafe.com" class="text-blue-600 hover:text-blue-800">sales@stafe.com</a> - Sales inquiries
                            </p>
                            <p class="text-gray-600">
                                <a href="mailto:support@stafe.com" class="text-blue-600 hover:text-blue-800">support@stafe.com</a> - Technical support
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Address</h3>
                            <p class="text-gray-600">
                                Stafe Group AB<br>
                                Stockholm, Sweden
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Business Hours</h3>
                            <p class="text-gray-600">
                                Monday - Friday: 9:00 AM - 6:00 PM CET<br>
                                Weekend: Support available
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-12 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-block text-center">
                        Start Free Trial
                    </a>
                    
                    <a href="{{ route('landing.pricing') }}" 
                       class="w-full bg-white text-gray-900 py-3 px-6 rounded-lg font-semibold border border-gray-300 hover:bg-gray-50 transition-colors inline-block text-center">
                        View Pricing
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Frequently Asked Questions
            </h2>
            <p class="text-xl text-gray-600">
                Common questions about Stafe CRM
            </p>
        </div>
        
        <div class="space-y-6">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    How long does it take to set up Stafe CRM?
                </h3>
                <p class="text-gray-600">
                    Most teams can get started with Stafe CRM within minutes. Our setup wizard guides you through the initial configuration, and our support team is available to help with data migration and advanced setup.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Do you offer training and onboarding?
                </h3>
                <p class="text-gray-600">
                    Yes! We provide comprehensive training resources, documentation, and personalized onboarding sessions for enterprise customers. Our goal is to ensure your team gets the most out of Stafe CRM.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Can I migrate data from my existing CRM?
                </h3>
                <p class="text-gray-600">
                    Absolutely! We support data migration from most popular CRM platforms. Our team can help you plan and execute a smooth migration with minimal disruption to your business.
                </p>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    What kind of support do you offer?
                </h3>
                <p class="text-gray-600">
                    We offer multiple support channels including email, chat, and phone support. Enterprise customers receive priority support with dedicated account managers and faster response times.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection