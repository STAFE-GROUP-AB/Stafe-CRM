# E-Signature Pad Implementation

## Quick Start

### 1. Use in Any Livewire Component

```blade
<x-signature-pad
    wire="signatureData"
    label="Customer Signature"
    :required="true"
/>
```

### 2. Handle in Livewire Component

```php
class YourComponent extends Component
{
    public $signatureData = null;

    public function save()
    {
        // Signature is already base64 encoded PNG
        $base64 = explode('base64,', $this->signatureData)[1];

        // Save to database or process
        QuoteSignature::create([
            'signature_data' => $base64,
            'signed_at' => now(),
        ]);
    }
}
```

## What's Included

### Files Created

1. **Blade Component**
   - `/resources/views/components/signature-pad.blade.php`
   - Reusable Alpine.js signature pad component

2. **Livewire Components**
   - `/app/Livewire/QuoteSignatureCapture.php`
   - `/resources/views/livewire/quote-signature-capture.blade.php`
   - Complete quote signature capture workflow

3. **Examples**
   - `/resources/views/examples/signature-pad-example.blade.php`
   - Multiple usage examples

4. **Documentation**
   - `/docs/signature-pad-component.md`
   - Comprehensive documentation

### Existing Database Structure

The signature tables are already migrated:
- `quote_signatures` table with all necessary fields
- `QuoteSignature` model with relationships

## Key Features

- No external dependencies (pure HTML5 Canvas + Alpine.js)
- Mouse and touch support (works on mobile)
- Dark mode support (automatic theme detection)
- Undo last stroke
- Clear all strokes
- Real-time validation
- Livewire integration
- Base64 PNG output
- Responsive design

## Component Props

```blade
<x-signature-pad
    wire="propertyName"          {{-- Livewire property to bind --}}
    name="signature"             {{-- Input name --}}
    :width="600"                 {{-- Canvas width (px) --}}
    :height="200"                {{-- Canvas height (px) --}}
    label="Signature"            {{-- Label text --}}
    :required="true"             {{-- Is required --}}
    :show-instructions="true"    {{-- Show "Sign here" text --}}
/>
```

## Usage Examples

### Example 1: Simple Quote Signature

```blade
<div>
    <h3>Sign to Approve</h3>

    <x-signature-pad
        wire="signature"
        label="Your Signature"
        :required="true"
    />

    <button wire:click="approve">
        Approve Quote
    </button>
</div>
```

### Example 2: Multi-Party Signatures

```blade
<div class="space-y-6">
    <div>
        <h4>Client Signature</h4>
        <x-signature-pad wire="clientSig" :required="true" />
    </div>

    <div>
        <h4>Witness Signature</h4>
        <x-signature-pad wire="witnessSig" :required="false" />
    </div>
</div>
```

### Example 3: Compact Initials

```blade
<x-signature-pad
    wire="initials"
    label="Initials"
    :width="200"
    :height="100"
    :show-instructions="false"
/>
```

## Integration with Quote Builder

### Add to Quote View

```blade
<!-- In quote approval section -->
@if($quote->status === 'sent')
    <div class="mt-6">
        <h3>Approve This Quote</h3>

        <form wire:submit.prevent="signQuote">
            <x-signature-pad
                wire="customerSignature"
                label="Customer Signature"
                :required="true"
            />

            <button type="submit">
                Sign and Approve
            </button>
        </form>
    </div>
@endif
```

### Add to Livewire Component

```php
// In QuoteBuilder.php
public $customerSignature = null;

public function signQuote()
{
    $this->validate([
        'customerSignature' => 'required|string',
    ]);

    $base64Data = explode('base64,', $this->customerSignature)[1];

    QuoteSignature::create([
        'quote_id' => $this->selectedQuote->id,
        'signature_data' => $base64Data,
        'signer_name' => auth()->user()->name,
        'signer_email' => auth()->user()->email,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'signature_type' => 'draw',
        'signed_at' => now(),
    ]);

    $this->selectedQuote->update([
        'status' => 'accepted',
        'accepted_at' => now(),
    ]);

    session()->flash('message', 'Quote approved successfully!');
}
```

## Displaying Saved Signatures

### In Blade Views

```blade
@if($quote->signatures->isNotEmpty())
    @foreach($quote->signatures as $signature)
        <div class="signature-record">
            <img
                src="{{ $signature->signature_image }}"
                alt="Signature by {{ $signature->signer_name }}"
                class="border rounded"
            />
            <p>
                Signed by {{ $signature->signer_name }}
                on {{ $signature->signed_at->format('M d, Y') }}
            </p>
        </div>
    @endforeach
@endif
```

### In PDFs

```php
use Barryvdh\DomPDF\Facade\Pdf;

$quote = Quote::with('signatures')->find($id);

$pdf = Pdf::loadView('quotes.pdf', [
    'quote' => $quote,
]);

// The signature_image accessor provides the data URL
// which works directly in img tags in PDFs
```

## Testing on Mobile

The signature pad works seamlessly on mobile devices:

1. Touch to start drawing
2. Move finger to draw signature
3. Lift finger to end stroke
4. Tap "Undo" to remove last stroke
5. Tap "Clear" to start over

Test on:
- iOS Safari
- Android Chrome
- Tablet devices

## Dark Mode

Dark mode is automatic:

- **Light theme**: Black ink on white canvas
- **Dark theme**: Light gray ink on dark canvas

The component detects:
1. Tailwind's `dark` class on `<html>`
2. System preference (`prefers-color-scheme`)
3. Theme toggles (watches DOM changes)

## Security Notes

1. Always validate server-side
2. Limit signature data size (max 500KB recommended)
3. Record IP address and user agent
4. Store timestamp for legal validity
5. Consider encryption for sensitive signatures

## Production Checklist

- [ ] Test on desktop (Chrome, Firefox, Safari)
- [ ] Test on mobile (iOS Safari, Android Chrome)
- [ ] Test dark mode toggle
- [ ] Test signature validation
- [ ] Test undo/clear functionality
- [ ] Verify server-side validation
- [ ] Test signature display in PDFs
- [ ] Verify database storage
- [ ] Test multi-signature forms
- [ ] Review security measures

## API Reference

### Alpine.js Methods

Access via Alpine's `$refs`:

```blade
<div x-data>
    <x-signature-pad wire="sig" x-ref="pad" />

    <button @click="$refs.pad.clear()">Clear</button>
    <button @click="$refs.pad.undo()">Undo</button>
</div>
```

Methods:
- `clear()` - Clear entire signature
- `undo()` - Remove last stroke
- `validate()` - Check if valid (returns boolean)
- `getSignatureData()` - Get base64 data URL

### Livewire Properties

The signature data is automatically synced to your Livewire property as a base64 data URL:

```
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA...
```

## Common Issues

### Signature not appearing
- Check that Alpine.js is loaded
- Verify dark mode isn't making signature invisible
- Check browser console for errors

### Livewire not updating
- Ensure property is public in component
- Check wire directive syntax
- Verify Alpine.js version compatibility

### Signature too small
- Increase width/height props
- Check parent container isn't constraining size

### Touch not working on mobile
- Ensure `touch-none` class is on canvas
- Check for conflicting touch handlers
- Test in actual mobile browser (not desktop emulation)

## Further Reading

- Full documentation: `/docs/signature-pad-component.md`
- Working examples: `/resources/views/examples/signature-pad-example.blade.php`
- Quote integration: `/resources/views/livewire/quote-signature-capture.blade.php`

## Credits

Built with:
- Alpine.js (reactive component)
- HTML5 Canvas API (signature drawing)
- Tailwind CSS 4 (styling)
- Laravel Livewire 3 (server integration)

No external signature libraries required.
