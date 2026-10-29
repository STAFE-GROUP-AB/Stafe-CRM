<div>
    <x-slot name="header">
        <div class="flex items-center">
            <button wire:click="goBack" class="mr-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </button>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $playbook->title }}
            </h2>
        </div>
    </x-slot>

    @php
        $primaryColor = team_theme()->primary();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header with Progress -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $playbook->title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($deal)
                                    {{ __('Deal') }}: {{ $deal->name }}
                                @elseif($contact)
                                    {{ __('Contact') }}: {{ $contact->first_name }} {{ $contact->last_name }}
                                @else
                                    {{ __('Practice Mode') }}
                                @endif
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0 flex items-center space-x-4">
                            @if($execution->isInProgress())
                                <button wire:click="openAbandonModal" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    {{ __('Abandon') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Progress') }}</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $progress }}%</span>
                        </div>
                        <div class="mt-2 h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $primaryColor }}-500 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $execution->getCompletedOnlySteps() }} {{ __('completed') }}</span>
                            <span>{{ $execution->getSkippedSteps() }} {{ __('skipped') }}</span>
                            <span>{{ $execution->getTotalSteps() }} {{ __('total') }}</span>
                        </div>
                    </div>

                    <!-- Time Tracking -->
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('Started') }}: {{ $execution->started_at->format('M d, Y H:i') }}</span>
                        <span class="mx-2">|</span>
                        <span>{{ __('Time spent') }}: {{ $execution->getFormattedTotalDuration() }}</span>
                    </div>
                </div>
            </div>

            <!-- Steps List -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($stepsWithExecutions as $index => $item)
                        @php
                            $step = $item['step'];
                            $stepExecution = $item['execution'];
                            $isCompleted = $stepExecution && $stepExecution->isCompleted();
                            $isSkipped = $stepExecution && $stepExecution->isSkipped();
                            $isInProgress = $stepExecution && $stepExecution->isInProgress();
                            $isPending = !$stepExecution || $stepExecution->isPending();
                            $isCurrent = $currentStep && $currentStep->id === $step->id;
                        @endphp

                        <div class="p-6 {{ $isCurrent ? 'bg-' . $primaryColor . '-50 dark:bg-' . $primaryColor . '-900/20' : '' }}">
                            <div class="flex items-start">
                                <!-- Step Status Icon -->
                                <div class="flex-shrink-0 mr-4">
                                    @if($isCompleted)
                                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @elseif($isSkipped)
                                        <div class="h-10 w-10 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    @elseif($isInProgress)
                                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center animate-pulse">
                                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $index + 1 }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Step Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ $step->title }}
                                            @if($step->is_required)
                                                <span class="ml-2 text-xs text-red-500">*</span>
                                            @endif
                                        </h4>
                                        <div class="flex items-center space-x-2">
                                            @if($step->estimated_duration_minutes)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    ~{{ $step->estimated_duration_minutes }} min
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $step->getTypeColor() }}-100 text-{{ $step->getTypeColor() }}-800 dark:bg-{{ $step->getTypeColor() }}-900 dark:text-{{ $step->getTypeColor() }}-200">
                                                {{ ucfirst($step->step_type) }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($step->description)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $step->description }}
                                        </p>
                                    @endif

                                    @if($isCompleted || $isSkipped || $isInProgress || $isCurrent)
                                        <!-- Step Details (expanded) -->
                                        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            @if($step->instructions)
                                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                                    {!! nl2br(e($step->instructions)) !!}
                                                </div>
                                            @endif

                                            @if($step->success_criteria)
                                                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded border border-green-200 dark:border-green-800">
                                                    <h5 class="text-sm font-medium text-green-800 dark:text-green-200">{{ __('Success Criteria') }}</h5>
                                                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">{{ $step->success_criteria }}</p>
                                                </div>
                                            @endif

                                            @if($step->resources && count($step->resources) > 0)
                                                <div class="mt-4">
                                                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Resources') }}</h5>
                                                    <ul class="mt-2 space-y-1">
                                                        @foreach($step->resources as $resource)
                                                            <li class="text-sm text-{{ $primaryColor }}-600 dark:text-{{ $primaryColor }}-400 hover:underline">
                                                                <a href="{{ $resource['url'] ?? '#' }}" target="_blank">{{ $resource['title'] ?? $resource }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Execution Details -->
                                        @if($stepExecution)
                                            <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                @if($isCompleted)
                                                    <svg class="h-4 w-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    {{ __('Completed') }} {{ $stepExecution->completed_at->diffForHumans() }}
                                                    @if($stepExecution->duration_seconds)
                                                        <span class="mx-2">|</span>
                                                        {{ __('Duration') }}: {{ $stepExecution->getFormattedDuration() }}
                                                    @endif
                                                @elseif($isSkipped)
                                                    <svg class="h-4 w-4 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                                    </svg>
                                                    {{ __('Skipped') }}: {{ $stepExecution->skipped_reason }}
                                                @elseif($isInProgress)
                                                    <svg class="h-4 w-4 mr-1 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    {{ __('In progress') }} - {{ __('started') }} {{ $stepExecution->started_at->diffForHumans() }}
                                                @endif
                                            </div>

                                            @if($stepExecution->notes)
                                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-600 dark:text-gray-400">
                                                    <span class="font-medium">{{ __('Notes') }}:</span> {{ $stepExecution->notes }}
                                                </div>
                                            @endif
                                        @endif
                                    @endif

                                    <!-- Action Buttons -->
                                    @if($execution->isInProgress())
                                        <div class="mt-4 flex items-center space-x-3">
                                            @if($isPending && $isCurrent)
                                                <button wire:click="startStep({{ $step->id }})" class="inline-flex items-center px-4 py-2 bg-{{ $primaryColor }}-600 hover:bg-{{ $primaryColor }}-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    </svg>
                                                    {{ __('Start Step') }}
                                                </button>
                                            @elseif($isInProgress)
                                                <button wire:click="openCompleteModal({{ $step->id }})" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    {{ __('Complete Step') }}
                                                </button>
                                                @if(!$step->is_required)
                                                    <button wire:click="openSkipModal({{ $step->id }})" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                                        </svg>
                                                        {{ __('Skip') }}
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Completed State -->
            @if($execution->isCompleted())
                <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-green-800 dark:text-green-200">{{ __('Playbook Completed!') }}</h3>
                            <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                {{ __('Completed at') }}: {{ $execution->completed_at->format('M d, Y H:i') }}
                                <span class="mx-2">|</span>
                                {{ __('Duration') }}: {{ $execution->duration_minutes }} {{ __('minutes') }}
                                <span class="mx-2">|</span>
                                {{ __('Outcome') }}: {{ ucfirst($execution->outcome) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Complete Step Modal -->
    <x-dialog-modal wire:model="showCompleteModal">
        <x-slot name="title">
            {{ __('Complete Step') }}
        </x-slot>

        <x-slot name="content">
            @if($currentStep)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Mark') }} <strong>"{{ $currentStep->title }}"</strong> {{ __('as completed?') }}
                </p>
            @endif

            <div>
                <x-label for="stepNotes" value="{{ __('Notes (optional)') }}" />
                <textarea wire:model="stepNotes" id="stepNotes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" placeholder="{{ __('Any notes about this step...') }}"></textarea>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCompleteModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button wire:click="completeStep" class="ms-3 bg-green-600 hover:bg-green-700">
                {{ __('Complete Step') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Skip Step Modal -->
    <x-dialog-modal wire:model="showSkipModal">
        <x-slot name="title">
            {{ __('Skip Step') }}
        </x-slot>

        <x-slot name="content">
            @if($currentStep)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Why are you skipping') }} <strong>"{{ $currentStep->title }}"</strong>?
                </p>
            @endif

            <div class="space-y-4">
                <div>
                    <x-label for="skipReason" value="{{ __('Reason (required)') }}" />
                    <select wire:model="skipReason" id="skipReason" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500">
                        <option value="">{{ __('Select a reason...') }}</option>
                        <option value="Not applicable">{{ __('Not applicable to this situation') }}</option>
                        <option value="Already completed">{{ __('Already completed previously') }}</option>
                        <option value="Customer declined">{{ __('Customer declined') }}</option>
                        <option value="Time constraint">{{ __('Time constraint') }}</option>
                        <option value="Other">{{ __('Other') }}</option>
                    </select>
                    <x-input-error for="skipReason" class="mt-2" />
                </div>

                <div>
                    <x-label for="skipNotes" value="{{ __('Additional notes (optional)') }}" />
                    <textarea wire:model="stepNotes" id="skipNotes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" placeholder="{{ __('Any additional context...') }}"></textarea>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showSkipModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button wire:click="skipStep" class="ms-3 bg-yellow-500 hover:bg-yellow-600">
                {{ __('Skip Step') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Abandon Execution Modal -->
    <x-confirmation-modal wire:model="showAbandonModal">
        <x-slot name="title">
            {{ __('Abandon Playbook') }}
        </x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('Are you sure you want to abandon this playbook execution? This action cannot be undone.') }}
            </p>

            <div>
                <x-label for="abandonReason" value="{{ __('Reason (optional)') }}" />
                <textarea wire:model="abandonReason" id="abandonReason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" placeholder="{{ __('Why are you abandoning this playbook?') }}"></textarea>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAbandonModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button wire:click="abandonExecution" class="ms-3">
                {{ __('Abandon') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

    <!-- Rating Modal -->
    <x-dialog-modal wire:model="showRatingModal">
        <x-slot name="title">
            {{ __('Rate this Playbook') }}
        </x-slot>

        <x-slot name="content">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('How helpful was this playbook? Your feedback helps us improve.') }}
            </p>

            <div class="space-y-4">
                <div>
                    <x-label value="{{ __('Rating') }}" />
                    <div class="mt-2 flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none">
                                <svg class="h-8 w-8 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                </div>

                <div>
                    <x-label for="feedback" value="{{ __('Feedback (optional)') }}" />
                    <textarea wire:model="feedback" id="feedback" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500" placeholder="{{ __('What could be improved?') }}"></textarea>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="skipRating">
                {{ __('Skip') }}
            </x-secondary-button>

            <x-button wire:click="submitRating" class="ms-3">
                {{ __('Submit Rating') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
