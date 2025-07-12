<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900">Create New Deal</h1>
                <p class="text-sm text-gray-600 mt-1">Fill out the form below to create a new deal</p>
            </div>
            
            <form action="{{ route('deals.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="space-y-6">
                    <!-- Deal Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Deal Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter deal name">
                    </div>

                    <!-- Deal Value -->
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                            Deal Value
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" 
                                   id="value" 
                                   name="value" 
                                   step="0.01"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Company -->
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Company
                        </label>
                        <select id="company_id" 
                                name="company_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a company</option>
                            <!-- Companies will be populated here -->
                        </select>
                    </div>

                    <!-- Contact -->
                    <div>
                        <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Primary Contact
                        </label>
                        <select id="contact_id" 
                                name="contact_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a contact</option>
                            <!-- Contacts will be populated here -->
                        </select>
                    </div>

                    <!-- Pipeline Stage -->
                    <div>
                        <label for="pipeline_stage_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pipeline Stage
                        </label>
                        <select id="pipeline_stage_id" 
                                name="pipeline_stage_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a stage</option>
                            <option value="1">Lead</option>
                            <option value="2">Qualified</option>
                            <option value="3">Proposal</option>
                            <option value="4">Negotiation</option>
                            <option value="5">Closed Won</option>
                            <option value="6">Closed Lost</option>
                        </select>
                    </div>

                    <!-- Expected Close Date -->
                    <div>
                        <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expected Close Date
                        </label>
                        <input type="date" 
                               id="expected_close_date" 
                               name="expected_close_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Probability -->
                    <div>
                        <label for="probability" class="block text-sm font-medium text-gray-700 mb-2">
                            Probability (%)
                        </label>
                        <input type="number" 
                               id="probability" 
                               name="probability" 
                               min="0" 
                               max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="50">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Enter deal description..."></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('deals.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                        Create Deal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>