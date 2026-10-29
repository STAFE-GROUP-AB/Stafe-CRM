# E-Signature Pad Component Documentation

## Overview

The Signature Pad component is a production-ready, Alpine.js-powered component for capturing electronic signatures in Stafe CRM. It provides a smooth, responsive signature capture experience with full dark mode support and works seamlessly with Livewire.

## Features

- HTML5 Canvas-based signature capture (no external dependencies)
- Desktop (mouse) and mobile (touch) support
- Clear and undo functionality
- Real-time Livewire integration
- Full dark mode support with automatic theme detection
- Responsive sizing
- Visual feedback during signing
- Signature validation
- Accessibility features
- Base64 PNG output

## Installation

The signature pad component is already installed at:
```
/resources/views/components/signature-pad.blade.php
```

No additional packages or dependencies are required.

## Basic Usage

### Simple Integration

```blade
<x-signature-pad
    wire="signatureData"
    name="customer_signature"
    label="Customer Signature"
    :required="true"
/>
```

### With Livewire Component

```php
// In your Livewire component
class QuoteApproval extends Component
{
    public $signatureData = null;

    public function saveSignature()
    {
        $this->validate([
            'signatureData' => 'required|string',
        ]);

        // Process signature...
    }
}
```

```blade
<!-- In your view -->
<x-signature-pad
    wire="signatureData"
    label="Approve Quote"
    :required="true"
/>

<button wire:click="saveSignature">
    Submit Signature
</button>
```

## Component Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `wire` | string | null | Livewire property name to bind signature data |
| `name` | string | 'signature' | Input name for the signature field |
| `width` | int | 600 | Canvas width in pixels |
| `height` | int | 200 | Canvas height in pixels |
| `label` | string | 'Signature' | Label text displayed above the pad |
| `required` | boolean | false | Whether signature is required |
| `showInstructions` | boolean | true | Show "Sign here" placeholder text |

## Advanced Usage

### Custom Dimensions

```blade
<x-signature-pad
    wire="approverSignature"
    :width="800"
    :height="300"
    label="Executive Approval"
/>
```

### Multiple Signatures

```blade
<div class="space-y-6">
    <x-signature-pad
        wire="clientSignature"
        label="Client Signature"
        :required="true"
    />

    <x-signature-pad
        wire="witnessSignature"
        label="Witness Signature"
        :required="false"
    />
</div>
```

### Compact Mode

```blade
<x-signature-pad
    wire="initialsSignature"
    :width="300"
    :height="100"
    label="Initials"
    :show-instructions="false"
/>
```

## JavaScript API

The signature pad exposes several Alpine.js methods you can call:

### Methods

#### `clear()`
Clears the signature canvas completely.

```html
<div x-data>
    <x-signature-pad wire="signature" />
    <button @click="$refs.signaturePad.clear()">
        Clear Signature
    </button>
</div>
```

#### `undo()`
Removes the last stroke from the signature.

#### `validate()`
Validates that a signature exists (if required).

Returns: `boolean`

#### `getSignatureData()`
Returns the current signature as a base64-encoded PNG data URL.

Returns: `string | null`

### Properties

#### `hasSignature`
Boolean indicating whether a signature has been drawn.

#### `isDrawing`
Boolean indicating whether user is currently drawing.

#### `showError`
Boolean indicating whether validation error should be shown.

## Data Storage

### Base64 Format

The signature is captured as a base64-encoded PNG image:

```
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA...
```

### Saving to Database

#### Option 1: Store Base64 in Database

```php
// Extract base64 data (remove data URL prefix)
$signatureBase64 = explode('base64,', $this->signatureData)[1];

QuoteSignature::create([
    'quote_id' => $quote->id,
    'signature_data' => $signatureBase64,
    'signed_at' => now(),
]);
```

#### Option 2: Store as File

```php
use Illuminate\Support\Facades\Storage;

// Decode base64 and save as file
$signatureBase64 = explode('base64,', $this->signatureData)[1];
$imageData = base64_decode($signatureBase64);
$filename = 'signatures/' . uniqid() . '.png';

Storage::disk('public')->put($filename, $imageData);

QuoteSignature::create([
    'quote_id' => $quote->id,
    'signature_path' => $filename,
    'signed_at' => now(),
]);
```

## Database Schema

The `quote_signatures` table is already migrated with this schema:

```php
Schema::create('quote_signatures', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained()->onDelete('cascade');
    $table->foreignId('signer_id')->nullable()->constrained('users');
    $table->string('signer_name');
    $table->string('signer_email');
    $table->string('signer_title')->nullable();
    $table->text('signature_data'); // Base64 encoded
    $table->string('ip_address');
    $table->string('user_agent');
    $table->enum('signature_type', ['draw', 'type', 'upload'])->default('draw');
    $table->timestamp('signed_at');
    $table->timestamps();
});
```

## QuoteSignature Model

### Retrieving Signature Image

```php
$signature = QuoteSignature::find($id);

// Get as data URL (ready for <img> tag)
$dataUrl = $signature->getSignatureImageAttribute();

// Use in view
<img src="{{ $signature->signature_image }}" alt="Signature" />
```

### Model Accessor

```php
public function getSignatureImageAttribute(): string
{
    return 'data:image/png;base64,' . $this->signature_data;
}
```

## Dark Mode Support

The signature pad automatically adapts to dark mode:

- **Light Mode**: Black ink on white background
- **Dark Mode**: Light gray ink on dark background

The component listens for:
1. CSS class changes on `<html>` element
2. System preference changes (`prefers-color-scheme`)

No configuration needed - it just works!

## Responsive Behavior

The canvas is responsive and will scale to fit its container while maintaining aspect ratio:

```blade
<!-- Full width on mobile, constrained on desktop -->
<div class="max-w-2xl">
    <x-signature-pad
        wire="signature"
        :width="600"
        :height="200"
    />
</div>
```

The canvas uses `max-width: 100%` to ensure it never overflows its container.

## Validation

### Client-Side Validation

The component provides built-in client-side validation:

```blade
<form x-data="{ validateSignature() { return $refs.sigPad.validate(); } }">
    <x-signature-pad wire="signature" :required="true" x-ref="sigPad" />

    <button @click.prevent="validateSignature() && $wire.submit()">
        Submit
    </button>
</form>
```

### Server-Side Validation

Always validate on the server:

```php
public function saveSignature()
{
    $this->validate([
        'signatureData' => [
            'required',
            'string',
            'regex:/^data:image\/png;base64,/',
        ],
    ]);

    // Process signature...
}
```

## Security Best Practices

1. **Always validate server-side** - Never trust client-side validation alone
2. **Record metadata** - Store IP address, user agent, and timestamp
3. **Verify file size** - Limit base64 string length to prevent abuse
4. **Sanitize before display** - Use appropriate escaping when displaying signatures

### Example Security Implementation

```php
public function saveSignature()
{
    // Validate
    $this->validate([
        'signatureData' => [
            'required',
            'string',
            'max:500000', // ~500KB limit
            'regex:/^data:image\/png;base64,/',
        ],
        'signerEmail' => 'required|email',
    ]);

    // Extract and validate base64
    $base64Data = explode('base64,', $this->signatureData)[1];

    if (!$base64Data || !base64_decode($base64Data, true)) {
        throw ValidationException::withMessages([
            'signatureData' => 'Invalid signature data.',
        ]);
    }

    // Save with metadata
    QuoteSignature::create([
        'quote_id' => $this->quote->id,
        'signature_data' => $base64Data,
        'signer_email' => $this->signerEmail,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'signed_at' => now(),
    ]);
}
```

## Complete Example: Quote Signature Flow

### 1. Livewire Component

```php
// app/Livewire/QuoteSignatureCapture.php
class QuoteSignatureCapture extends Component
{
    public Quote $quote;
    public $signerName = '';
    public $signerEmail = '';
    public $signatureData = null;

    public function saveSignature()
    {
        $this->validate([
            'signerName' => 'required|string|max:255',
            'signerEmail' => 'required|email',
            'signatureData' => 'required|string',
        ]);

        $base64Data = explode('base64,', $this->signatureData)[1];

        QuoteSignature::create([
            'quote_id' => $this->quote->id,
            'signer_name' => $this->signerName,
            'signer_email' => $this->signerEmail,
            'signature_data' => $base64Data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'signed_at' => now(),
        ]);

        $this->quote->update(['status' => 'accepted']);

        session()->flash('message', 'Quote signed successfully.');
        return redirect()->route('quotes.view', $this->quote);
    }
}
```

### 2. View

```blade
<!-- resources/views/livewire/quote-signature-capture.blade.php -->
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Sign Quote #{{ $quote->quote_number }}</h2>

        <form wire:submit.prevent="saveSignature">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Name</label>
                    <input wire:model="signerName" type="text" class="w-full rounded-md" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input wire:model="signerEmail" type="email" class="w-full rounded-md" required>
                </div>

                <x-signature-pad
                    wire="signatureData"
                    label="Your Signature"
                    :required="true"
                />

                <button type="submit" class="px-6 py-3 bg-amber-600 text-white rounded-md">
                    Sign and Submit
                </button>
            </div>
        </form>
    </div>
</div>
```

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support (iOS 13+)
- Mobile browsers: Full touch support

## Performance Considerations

- Canvas operations are lightweight and fast
- Base64 encoding happens on submission only
- Undo functionality maintains stroke history (memory efficient)
- Signature data is ~10-50KB typically

## Accessibility

- Proper ARIA labels
- Keyboard-accessible controls
- Clear visual feedback
- Error messaging
- High contrast support in dark mode

## Troubleshooting

### Signature not showing in dark mode
Ensure your dark mode is properly configured in Tailwind and the `dark` class is applied to `<html>`.

### Canvas too small on mobile
The canvas automatically scales. Ensure parent containers don't have `overflow: hidden` or fixed widths.

### Livewire property not updating
Check that you're using the correct wire property name and that it's public in your component.

### Signature appears pixelated
Increase canvas dimensions via `width` and `height` props for higher resolution.

## Examples

See complete working examples in:
- `/resources/views/examples/signature-pad-example.blade.php`
- `/resources/views/livewire/quote-signature-capture.blade.php`

## Support

For issues or questions, refer to the Stafe CRM documentation or contact the development team.
