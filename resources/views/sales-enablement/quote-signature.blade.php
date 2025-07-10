<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Signature - {{ $quote->quote_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-300 to-indigo-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
            <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
                <div class="max-w-md mx-auto">
                    <div class="divide-y divide-gray-200">
                        <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                                Sign Quote {{ $quote->quote_number }}
                            </h2>
                            <p class="text-sm text-gray-600 text-center mb-6">
                                {{ $quote->title }} - {{ $quote->company->name }}
                            </p>
                            
                            <form id="signatureForm" method="POST" action="{{ route('sales-enablement.quotes.signature', $quote) }}">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Your Name</label>
                                        <input type="text" name="signer_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                        <input type="email" name="signer_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title/Position</label>
                                        <input type="text" name="signer_title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Signature</label>
                                        <div class="border-2 border-gray-300 rounded-lg">
                                            <canvas id="signaturePad" width="400" height="200" class="rounded-lg cursor-crosshair"></canvas>
                                        </div>
                                        <div class="mt-2 flex justify-between">
                                            <button type="button" id="clearSignature" class="text-sm text-gray-600 hover:text-gray-800">
                                                Clear
                                            </button>
                                            <span class="text-xs text-gray-500">Sign above</span>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="signature_data" id="signatureData">
                                    <input type="hidden" name="signature_type" value="draw">
                                    
                                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Sign Quote
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Signature Pad Implementation
        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasSignature = false;

        // Set up canvas
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';

        // Mouse events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events for mobile
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        });

        canvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        });

        canvas.addEventListener('touchend', function(e) {
            e.preventDefault();
            const mouseEvent = new MouseEvent('mouseup', {});
            canvas.dispatchEvent(mouseEvent);
        });

        function startDrawing(e) {
            isDrawing = true;
            hasSignature = true;
            const rect = canvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        }

        function draw(e) {
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        // Clear signature
        document.getElementById('clearSignature').addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSignature = false;
        });

        // Form submission
        document.getElementById('signatureForm').addEventListener('submit', function(e) {
            if (!hasSignature) {
                e.preventDefault();
                alert('Please provide your signature before submitting.');
                return;
            }

            // Convert canvas to base64
            const signatureData = canvas.toDataURL('image/png');
            document.getElementById('signatureData').value = signatureData;
        });
    </script>
</body>
</html>