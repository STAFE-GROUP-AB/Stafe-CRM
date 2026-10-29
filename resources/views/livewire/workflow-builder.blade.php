<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-full mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('workflows.index') }}"
                       class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $workflow?->exists ? 'Edit' : 'Create' }} Workflow
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Drag nodes from the palette to build your automation workflow
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button"
                            wire:click="save"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Workflow
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="max-w-full mx-auto px-4 py-3 sm:px-6 lg:px-8">
            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="max-w-full mx-auto px-4 py-3 sm:px-6 lg:px-8">
            <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-md p-4">
                <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-full mx-auto px-4 py-6 sm:px-6 lg:px-8"
         x-data="workflowBuilder(@entangle('nodes'), @entangle('connections'))"
         x-init="init()">

        <div class="grid grid-cols-12 gap-6">
            <!-- Left Sidebar - Node Palette -->
            <div class="col-span-12 lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
                            Node Palette
                        </h3>
                    </div>

                    <div class="p-3 space-y-2">
                        <!-- Trigger Nodes -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Trigger</p>
                            <div draggable="true"
                                 @dragstart="startDrag($event, 'trigger', 'event')"
                                 class="group cursor-move bg-gradient-to-r from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                                    </svg>
                                    <span class="text-xs font-medium">Event Trigger</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Nodes -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Actions</p>

                            <div draggable="true"
                                 @dragstart="startDrag($event, 'action', 'send_email')"
                                 class="group cursor-move bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="text-xs font-medium">Send Email</span>
                                </div>
                            </div>

                            <div draggable="true"
                                 @dragstart="startDrag($event, 'action', 'create_task')"
                                 class="group cursor-move bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" />
                                    </svg>
                                    <span class="text-xs font-medium">Create Task</span>
                                </div>
                            </div>

                            <div draggable="true"
                                 @dragstart="startDrag($event, 'action', 'update_field')"
                                 class="group cursor-move bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    <span class="text-xs font-medium">Update Field</span>
                                </div>
                            </div>

                            <div draggable="true"
                                 @dragstart="startDrag($event, 'action', 'send_notification')"
                                 class="group cursor-move bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                                    </svg>
                                    <span class="text-xs font-medium">Send Notification</span>
                                </div>
                            </div>
                        </div>

                        <!-- Condition Nodes -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Logic</p>

                            <div draggable="true"
                                 @dragstart="startDrag($event, 'condition', 'if_else')"
                                 class="group cursor-move bg-gradient-to-r from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                    </svg>
                                    <span class="text-xs font-medium">If/Else Branch</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delay Nodes -->
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Timing</p>

                            <div draggable="true"
                                 @dragstart="startDrag($event, 'delay', 'wait')"
                                 class="group cursor-move bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 text-white rounded-md p-3 mb-2 hover:shadow-lg transition-all duration-200 hover:scale-105">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                                    </svg>
                                    <span class="text-xs font-medium">Wait/Delay</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center - Canvas -->
            <div class="col-span-12 lg:col-span-7">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Workflow Canvas
                        </h3>
                        <div class="flex items-center space-x-2">
                            <button @click="zoomOut"
                                    class="p-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                                </svg>
                            </button>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="Math.round(zoom * 100) + '%'"></span>
                            <button @click="zoomIn"
                                    class="p-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </button>
                            <button @click="resetView"
                                    class="ml-2 px-2 py-1 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors duration-200">
                                Reset View
                            </button>
                        </div>
                    </div>

                    <!-- Canvas Area -->
                    <div class="relative bg-gray-50 dark:bg-gray-900 overflow-hidden"
                         style="height: calc(100vh - 300px); min-height: 600px;">

                        <!-- Grid Background -->
                        <svg class="absolute inset-0 w-full h-full pointer-events-none"
                             :style="`transform: scale(${zoom}) translate(${panX}px, ${panY}px); transform-origin: 0 0;`">
                            <defs>
                                <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                                    <circle cx="1" cy="1" r="1" class="fill-gray-300 dark:fill-gray-700" />
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#grid)" />
                        </svg>

                        <!-- Canvas with Pan/Zoom -->
                        <div class="absolute inset-0 cursor-grab active:cursor-grabbing"
                             @mousedown="startPan"
                             @mousemove="doPan"
                             @mouseup="endPan"
                             @mouseleave="endPan"
                             @drop.prevent="handleDrop"
                             @dragover.prevent>

                            <div :style="`transform: scale(${zoom}) translate(${panX}px, ${panY}px); transform-origin: 0 0; transition: transform 0.1s ease-out;`"
                                 class="relative w-full h-full">

                                <!-- SVG for Connections -->
                                <svg class="absolute inset-0 w-full h-full pointer-events-none"
                                     style="min-width: 2000px; min-height: 2000px;">
                                    <template x-for="connection in connections" :key="connection.id">
                                        <g>
                                            <path :d="getConnectionPath(connection)"
                                                  stroke="currentColor"
                                                  class="stroke-gray-400 dark:stroke-gray-600 hover:stroke-amber-500 dark:hover:stroke-amber-400 cursor-pointer pointer-events-auto transition-colors duration-200"
                                                  stroke-width="2"
                                                  fill="none"
                                                  @click="deleteConnection(connection.id)">
                                            </path>
                                            <!-- Arrow Head -->
                                            <circle :cx="getArrowPosition(connection).x"
                                                   :cy="getArrowPosition(connection).y"
                                                   r="4"
                                                   class="fill-gray-400 dark:fill-gray-600" />
                                        </g>
                                    </template>

                                    <!-- Temporary Connection Line -->
                                    <template x-if="connectingFrom">
                                        <path :d="`M ${connectingFrom.x} ${connectingFrom.y} L ${mouseX} ${mouseY}`"
                                              stroke="currentColor"
                                              class="stroke-amber-500 dark:stroke-amber-400"
                                              stroke-width="2"
                                              stroke-dasharray="5,5"
                                              fill="none">
                                        </path>
                                    </template>
                                </svg>

                                <!-- Workflow Nodes -->
                                <template x-for="node in nodes" :key="node.id">
                                    <div :id="node.id"
                                         class="absolute cursor-move group"
                                         :class="[
                                             selectedNode === node.id ? 'ring-2 ring-amber-500 dark:ring-amber-400' : '',
                                             node.type === 'trigger' ? 'bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700' :
                                             node.type === 'action' ? 'bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700' :
                                             node.type === 'condition' ? 'bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700' :
                                             node.type === 'delay' ? 'bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700' :
                                             'bg-gray-500 dark:bg-gray-600'
                                         ]"
                                         :style="`left: ${node.x}px; top: ${node.y}px; width: 180px;`"
                                         @mousedown.stop="startDragNode($event, node.id)"
                                         @click.stop="selectNodeForEditing(node.id)">

                                        <div class="rounded-lg shadow-lg p-4 text-white relative">
                                            <!-- Delete Button -->
                                            <button @click.stop="deleteNode(node.id)"
                                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" />
                                                </svg>
                                            </button>

                                            <!-- Node Icon -->
                                            <div class="flex items-center mb-2">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <template x-if="node.type === 'trigger'">
                                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                                                    </template>
                                                    <template x-if="node.type === 'action'">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                    </template>
                                                    <template x-if="node.type === 'condition'">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                                    </template>
                                                    <template x-if="node.type === 'delay'">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                                                    </template>
                                                </svg>
                                                <span class="text-xs font-semibold uppercase tracking-wide" x-text="node.type"></span>
                                            </div>

                                            <!-- Node Label -->
                                            <p class="text-sm font-medium leading-tight mb-3" x-text="node.label || 'Unnamed Node'"></p>

                                            <!-- Connection Points -->
                                            <div class="flex justify-between">
                                                <!-- Input Port -->
                                                <div v-show="node.type !== 'trigger'"
                                                     @mouseup="endConnection(node.id, 'input')"
                                                     class="w-3 h-3 bg-white rounded-full border-2 border-white hover:scale-150 transition-transform duration-200 cursor-crosshair"
                                                     style="margin-left: -1.5rem; margin-top: 0.5rem;">
                                                </div>

                                                <!-- Output Port -->
                                                <div @mousedown.stop="startConnection($event, node.id, 'output')"
                                                     class="w-3 h-3 bg-white rounded-full border-2 border-white hover:scale-150 transition-transform duration-200 cursor-crosshair ml-auto"
                                                     style="margin-right: -1.5rem; margin-top: 0.5rem;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Configuration -->
            <div class="col-span-12 lg:col-span-3">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                    <!-- Workflow Settings Tab -->
                    <div x-data="{ activeTab: 'workflow' }">
                        <div class="flex border-b border-gray-200 dark:border-gray-700">
                            <button @click="activeTab = 'workflow'"
                                    :class="activeTab === 'workflow' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                    class="flex-1 px-4 py-3 text-xs font-medium border-b-2 transition-colors duration-200">
                                Workflow
                            </button>
                            <button @click="activeTab = 'node'"
                                    :class="activeTab === 'node' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                    class="flex-1 px-4 py-3 text-xs font-medium border-b-2 transition-colors duration-200">
                                Node Config
                            </button>
                        </div>

                        <!-- Workflow Settings -->
                        <div x-show="activeTab === 'workflow'" class="p-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Workflow Name
                                </label>
                                <input type="text"
                                       wire:model="workflowName"
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200"
                                       placeholder="My Workflow">
                                @error('workflowName')
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description
                                </label>
                                <textarea wire:model="workflowDescription"
                                          rows="3"
                                          class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200"
                                          placeholder="Describe what this workflow does..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Trigger Type
                                </label>
                                <select wire:model="triggerType"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200">
                                    <option value="event">Event-based</option>
                                    <option value="schedule">Scheduled</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model="isActive"
                                           class="rounded border-gray-300 dark:border-gray-600 text-amber-600 shadow-sm focus:ring-amber-500 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Workflow is active</span>
                                </label>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                    <p class="flex items-center justify-between">
                                        <span>Total Nodes:</span>
                                        <span class="font-medium" x-text="nodes.length"></span>
                                    </p>
                                    <p class="flex items-center justify-between">
                                        <span>Connections:</span>
                                        <span class="font-medium" x-text="connections.length"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Node Configuration -->
                        <div x-show="activeTab === 'node'" class="p-4">
                            <div x-show="selectedNode" x-cloak>
                                <div class="space-y-4" x-data="nodeConfigEditor()">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                                        Configure Node
                                    </h4>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Node Label
                                        </label>
                                        <input type="text"
                                               x-model="nodeLabel"
                                               @input="updateNodeLabel"
                                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200"
                                               placeholder="Enter node name">
                                    </div>

                                    <!-- Action Type Config -->
                                    <template x-if="getSelectedNodeType() === 'action'">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Action Details
                                            </label>
                                            <textarea x-model="actionDetails"
                                                      rows="4"
                                                      class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200"
                                                      placeholder="Configure action settings..."></textarea>
                                        </div>
                                    </template>

                                    <!-- Condition Type Config -->
                                    <template x-if="getSelectedNodeType() === 'condition'">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Condition
                                            </label>
                                            <select x-model="conditionField"
                                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200 mb-2">
                                                <option value="">Select field...</option>
                                                <option value="status">Status</option>
                                                <option value="priority">Priority</option>
                                                <option value="amount">Amount</option>
                                            </select>

                                            <select x-model="conditionOperator"
                                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200 mb-2">
                                                <option value="equals">Equals</option>
                                                <option value="not_equals">Not Equals</option>
                                                <option value="greater_than">Greater Than</option>
                                                <option value="less_than">Less Than</option>
                                            </select>

                                            <input type="text"
                                                   x-model="conditionValue"
                                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200"
                                                   placeholder="Value">
                                        </div>
                                    </template>

                                    <!-- Delay Type Config -->
                                    <template x-if="getSelectedNodeType() === 'delay'">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Delay Duration
                                            </label>
                                            <div class="flex space-x-2">
                                                <input type="number"
                                                       x-model="delayAmount"
                                                       min="1"
                                                       class="flex-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200"
                                                       placeholder="1">
                                                <select x-model="delayUnit"
                                                        class="border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 transition-all duration-200">
                                                    <option value="minutes">Minutes</option>
                                                    <option value="hours">Hours</option>
                                                    <option value="days">Days</option>
                                                </select>
                                            </div>
                                        </div>
                                    </template>

                                    <button @click="saveNodeConfig"
                                            class="w-full mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600 text-white rounded-md text-sm font-medium transition-all duration-200">
                                        Save Configuration
                                    </button>
                                </div>
                            </div>

                            <div x-show="!selectedNode" class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Select a node to configure
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function workflowBuilder(nodesWire, connectionsWire) {
    return {
        nodes: nodesWire,
        connections: connectionsWire,

        dragType: null,
        dragSubtype: null,

        selectedNode: null,
        draggingNode: null,
        dragOffset: { x: 0, y: 0 },

        connectingFrom: null,
        mouseX: 0,
        mouseY: 0,

        zoom: 1,
        panX: 0,
        panY: 0,
        isPanning: false,
        panStart: { x: 0, y: 0 },

        init() {
            document.addEventListener('mousemove', (e) => {
                this.mouseX = (e.clientX - this.panX) / this.zoom;
                this.mouseY = (e.clientY - this.panY) / this.zoom;
            });
        },

        startDrag(event, type, subtype) {
            this.dragType = type;
            this.dragSubtype = subtype;
            event.dataTransfer.effectAllowed = 'copy';
        },

        handleDrop(event) {
            if (!this.dragType) return;

            const rect = event.currentTarget.getBoundingClientRect();
            const x = (event.clientX - rect.left - this.panX) / this.zoom;
            const y = (event.clientY - rect.top - this.panY) / this.zoom;

            const nodeId = 'node-' + Date.now();
            const label = this.dragSubtype.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');

            const newNode = {
                id: nodeId,
                type: this.dragType,
                subtype: this.dragSubtype,
                label: label,
                x: Math.max(0, x - 90),
                y: Math.max(0, y - 40),
                config: {}
            };

            this.nodes.push(newNode);

            this.dragType = null;
            this.dragSubtype = null;
        },

        startDragNode(event, nodeId) {
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;

            this.draggingNode = nodeId;
            this.dragOffset = {
                x: event.clientX / this.zoom - node.x,
                y: event.clientY / this.zoom - node.y
            };

            const moveHandler = (e) => {
                if (this.draggingNode) {
                    const node = this.nodes.find(n => n.id === this.draggingNode);
                    if (node) {
                        node.x = Math.max(0, e.clientX / this.zoom - this.dragOffset.x);
                        node.y = Math.max(0, e.clientY / this.zoom - this.dragOffset.y);
                    }
                }
            };

            const upHandler = () => {
                this.draggingNode = null;
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', upHandler);
            };

            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', upHandler);
        },

        selectNodeForEditing(nodeId) {
            this.selectedNode = nodeId;
            this.$wire.call('selectNode', nodeId);
        },

        deleteNode(nodeId) {
            if (confirm('Delete this node?')) {
                this.$wire.call('deleteNode', nodeId);
                if (this.selectedNode === nodeId) {
                    this.selectedNode = null;
                }
            }
        },

        startConnection(event, nodeId, port) {
            event.stopPropagation();
            const node = this.nodes.find(n => n.id === nodeId);
            if (!node) return;

            this.connectingFrom = {
                nodeId: nodeId,
                port: port,
                x: node.x + 180,
                y: node.y + 60
            };

            const moveHandler = (e) => {
                const rect = event.target.closest('.relative').getBoundingClientRect();
                this.mouseX = (e.clientX - rect.left - this.panX) / this.zoom;
                this.mouseY = (e.clientY - rect.top - this.panY) / this.zoom;
            };

            const upHandler = () => {
                this.connectingFrom = null;
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', upHandler);
            };

            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', upHandler);
        },

        endConnection(toNodeId, port) {
            if (!this.connectingFrom) return;

            const connectionId = `conn-${this.connectingFrom.nodeId}-${toNodeId}`;

            this.$wire.call('addConnection', {
                id: connectionId,
                from: this.connectingFrom.nodeId,
                to: toNodeId
            });

            this.connectingFrom = null;
        },

        deleteConnection(connectionId) {
            if (confirm('Delete this connection?')) {
                this.$wire.call('deleteConnection', connectionId);
            }
        },

        getConnectionPath(connection) {
            const fromNode = this.nodes.find(n => n.id === connection.from);
            const toNode = this.nodes.find(n => n.id === connection.to);

            if (!fromNode || !toNode) return '';

            const x1 = fromNode.x + 180;
            const y1 = fromNode.y + 60;
            const x2 = toNode.x;
            const y2 = toNode.y + 60;

            const midX = (x1 + x2) / 2;

            return `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
        },

        getArrowPosition(connection) {
            const toNode = this.nodes.find(n => n.id === connection.to);
            if (!toNode) return { x: 0, y: 0 };

            return {
                x: toNode.x - 5,
                y: toNode.y + 60
            };
        },

        zoomIn() {
            this.zoom = Math.min(this.zoom + 0.1, 2);
        },

        zoomOut() {
            this.zoom = Math.max(this.zoom - 0.1, 0.5);
        },

        resetView() {
            this.zoom = 1;
            this.panX = 0;
            this.panY = 0;
        },

        startPan(event) {
            if (event.target.closest('[draggable]') || event.target.closest('.cursor-move')) return;

            this.isPanning = true;
            this.panStart = { x: event.clientX - this.panX, y: event.clientY - this.panY };
        },

        doPan(event) {
            if (!this.isPanning) return;

            this.panX = event.clientX - this.panStart.x;
            this.panY = event.clientY - this.panStart.y;
        },

        endPan() {
            this.isPanning = false;
        }
    };
}

function nodeConfigEditor() {
    return {
        nodeLabel: '',
        actionDetails: '',
        conditionField: '',
        conditionOperator: 'equals',
        conditionValue: '',
        delayAmount: 1,
        delayUnit: 'hours',

        init() {
            this.$watch('$parent.selectedNode', (nodeId) => {
                if (nodeId) {
                    const node = this.$parent.nodes.find(n => n.id === nodeId);
                    if (node) {
                        this.nodeLabel = node.label || '';
                        this.actionDetails = node.config?.details || '';
                        this.conditionField = node.config?.field || '';
                        this.conditionOperator = node.config?.operator || 'equals';
                        this.conditionValue = node.config?.value || '';
                        this.delayAmount = node.config?.amount || 1;
                        this.delayUnit = node.config?.unit || 'hours';
                    }
                }
            });
        },

        getSelectedNodeType() {
            const node = this.$parent.nodes.find(n => n.id === this.$parent.selectedNode);
            return node?.type || '';
        },

        updateNodeLabel() {
            if (this.$parent.selectedNode) {
                this.$parent.updateNode(this.$parent.selectedNode, { label: this.nodeLabel });
            }
        },

        saveNodeConfig() {
            if (!this.$parent.selectedNode) return;

            const node = this.$parent.nodes.find(n => n.id === this.$parent.selectedNode);
            if (!node) return;

            let config = {};

            if (node.type === 'action') {
                config = { details: this.actionDetails };
            } else if (node.type === 'condition') {
                config = {
                    field: this.conditionField,
                    operator: this.conditionOperator,
                    value: this.conditionValue
                };
            } else if (node.type === 'delay') {
                config = {
                    amount: this.delayAmount,
                    unit: this.delayUnit
                };
            }

            this.$wire.call('updateNodeConfig', config);

            alert('Node configuration saved!');
        }
    };
}
</script>
@endpush
