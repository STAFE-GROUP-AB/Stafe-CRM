@props([
    'wire' => null,
    'name' => 'signature',
    'width' => 600,
    'height' => 200,
    'label' => 'Signature',
    'required' => false,
    'showInstructions' => true,
])

<div class="signature-pad-wrapper">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500 dark:text-red-400">*</span>
            @endif
        </label>
    @endif

    <div
        x-data="signaturePad({
            wire: @js($wire),
            name: @js($name),
            width: @js($width),
            height: @js($height),
            required: @js($required),
        })"
        x-init="init()"
        class="space-y-3"
    >
        <!-- Canvas Container -->
        <div class="relative">
            <canvas
                x-ref="canvas"
                :width="width"
                :height="height"
                @mousedown="startDrawing($event)"
                @mousemove="draw($event)"
                @mouseup="stopDrawing()"
                @mouseleave="stopDrawing()"
                @touchstart.prevent="handleTouchStart($event)"
                @touchmove.prevent="handleTouchMove($event)"
                @touchend.prevent="stopDrawing()"
                class="border-2 rounded-lg cursor-crosshair touch-none w-full"
                :class="{
                    'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700': !hasSignature,
                    'border-amber-500 dark:border-amber-400 bg-white dark:bg-gray-700': hasSignature,
                    'border-red-300 dark:border-red-600': showError
                }"
                style="max-width: 100%;"
            ></canvas>

            <!-- Instructions Overlay -->
            @if($showInstructions)
                <div
                    x-show="!hasSignature"
                    x-transition
                    class="absolute inset-0 flex items-center justify-center pointer-events-none"
                >
                    <p class="text-gray-400 dark:text-gray-500 text-sm font-medium">
                        Sign here
                    </p>
                </div>
            @endif

            <!-- Signature Activity Indicator -->
            <div
                x-show="isDrawing"
                x-transition.opacity
                class="absolute top-2 right-2 bg-amber-500 dark:bg-amber-400 text-white dark:text-gray-900 text-xs px-2 py-1 rounded-full"
            >
                Signing...
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <!-- Clear Button -->
                <button
                    type="button"
                    @click="clear()"
                    :disabled="!hasSignature"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear
                </button>

                <!-- Undo Button -->
                <button
                    type="button"
                    @click="undo()"
                    :disabled="strokes.length === 0"
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    Undo
                </button>
            </div>

            <!-- Status Indicator -->
            <div class="flex items-center">
                <template x-if="hasSignature">
                    <div class="flex items-center text-xs text-green-600 dark:text-green-400">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Signature captured
                    </div>
                </template>
                <template x-if="!hasSignature && showError">
                    <div class="flex items-center text-xs text-red-600 dark:text-red-400">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Signature required
                    </div>
                </template>
            </div>
        </div>

        <!-- Error Message -->
        <div x-show="showError" x-transition class="text-sm text-red-600 dark:text-red-400">
            Please provide your signature before submitting.
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('signaturePad', (config) => ({
        // Configuration
        wire: config.wire,
        name: config.name,
        width: config.width,
        height: config.height,
        required: config.required,

        // State
        canvas: null,
        ctx: null,
        isDrawing: false,
        hasSignature: false,
        showError: false,
        strokes: [],
        currentStroke: [],

        // Drawing properties
        lineWidth: 2,
        lineColor: null,

        init() {
            this.canvas = this.$refs.canvas;
            this.ctx = this.canvas.getContext('2d');

            // Set line color based on dark mode
            this.updateLineColor();

            // Configure canvas
            this.ctx.strokeStyle = this.lineColor;
            this.ctx.lineWidth = this.lineWidth;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';

            // Listen for dark mode changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                this.updateLineColor();
                this.redraw();
            });

            // Watch for manual dark mode toggle
            const observer = new MutationObserver(() => {
                this.updateLineColor();
                this.redraw();
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        },

        updateLineColor() {
            // Check if dark mode is active
            const isDark = document.documentElement.classList.contains('dark') ||
                          window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.lineColor = isDark ? '#f3f4f6' : '#000000'; // gray-100 for dark, black for light
        },

        startDrawing(e) {
            this.isDrawing = true;
            this.hasSignature = true;
            this.showError = false;
            this.currentStroke = [];

            const rect = this.canvas.getBoundingClientRect();
            const scaleX = this.canvas.width / rect.width;
            const scaleY = this.canvas.height / rect.height;
            const x = (e.clientX - rect.left) * scaleX;
            const y = (e.clientY - rect.top) * scaleY;

            this.ctx.beginPath();
            this.ctx.moveTo(x, y);
            this.currentStroke.push({ x, y, type: 'start' });
        },

        draw(e) {
            if (!this.isDrawing) {
                return;
            }

            const rect = this.canvas.getBoundingClientRect();
            const scaleX = this.canvas.width / rect.width;
            const scaleY = this.canvas.height / rect.height;
            const x = (e.clientX - rect.left) * scaleX;
            const y = (e.clientY - rect.top) * scaleY;

            this.ctx.strokeStyle = this.lineColor;
            this.ctx.lineTo(x, y);
            this.ctx.stroke();
            this.currentStroke.push({ x, y, type: 'draw' });
        },

        stopDrawing() {
            if (this.isDrawing && this.currentStroke.length > 0) {
                this.strokes.push([...this.currentStroke]);
                this.updateWireModel();
            }
            this.isDrawing = false;
            this.currentStroke = [];
        },

        handleTouchStart(e) {
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            this.canvas.dispatchEvent(mouseEvent);
        },

        handleTouchMove(e) {
            if (!this.isDrawing) {
                return;
            }
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            this.canvas.dispatchEvent(mouseEvent);
        },

        clear() {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            this.strokes = [];
            this.currentStroke = [];
            this.hasSignature = false;
            this.showError = false;
            this.updateWireModel();
        },

        undo() {
            if (this.strokes.length === 0) {
                return;
            }

            this.strokes.pop();

            if (this.strokes.length === 0) {
                this.hasSignature = false;
            }

            this.redraw();
            this.updateWireModel();
        },

        redraw() {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

            this.strokes.forEach(stroke => {
                this.ctx.strokeStyle = this.lineColor;
                this.ctx.lineWidth = this.lineWidth;
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';

                stroke.forEach((point, index) => {
                    if (index === 0 || point.type === 'start') {
                        this.ctx.beginPath();
                        this.ctx.moveTo(point.x, point.y);
                    } else {
                        this.ctx.lineTo(point.x, point.y);
                        this.ctx.stroke();
                    }
                });
            });
        },

        updateWireModel() {
            if (!this.wire) {
                return;
            }

            const data = this.hasSignature ? this.getSignatureData() : null;

            // Update Livewire property
            if (this.wire.startsWith('$parent.')) {
                const property = this.wire.replace('$parent.', '');
                this.$wire.$parent.set(property, data);
            } else {
                this.$wire.set(this.wire, data);
            }
        },

        getSignatureData() {
            if (!this.hasSignature) {
                return null;
            }

            // Convert canvas to base64 PNG
            return this.canvas.toDataURL('image/png');
        },

        validate() {
            if (this.required && !this.hasSignature) {
                this.showError = true;
                return false;
            }
            this.showError = false;
            return true;
        },
    }));
});
</script>
@endpush
