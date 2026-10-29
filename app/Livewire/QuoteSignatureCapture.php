<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\QuoteSignature;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class QuoteSignatureCapture extends Component
{
    public Quote $quote;

    public $signerName = '';
    public $signerEmail = '';
    public $signerTitle = '';
    public $signatureData = null;
    public $signatureType = 'draw';

    public function mount(Quote $quote)
    {
        $this->quote = $quote;

        // Pre-fill signer information if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $this->signerName = $user->name;
            $this->signerEmail = $user->email;
        }
    }

    public function render()
    {
        return view('livewire.quote-signature-capture');
    }

    public function saveSignature()
    {
        $this->validate([
            'signerName' => 'required|string|max:255',
            'signerEmail' => 'required|email|max:255',
            'signerTitle' => 'nullable|string|max:255',
            'signatureData' => 'required|string',
        ]);

        // Extract base64 data from data URL
        $signatureBase64 = $this->extractBase64FromDataUrl($this->signatureData);

        // Create signature record
        $signature = QuoteSignature::create([
            'quote_id' => $this->quote->id,
            'signer_id' => Auth::id(),
            'signer_name' => $this->signerName,
            'signer_email' => $this->signerEmail,
            'signer_title' => $this->signerTitle,
            'signature_data' => $signatureBase64,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'signature_type' => $this->signatureType,
            'signed_at' => now(),
        ]);

        // Update quote status
        $this->quote->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        session()->flash('message', 'Signature saved successfully.');

        // Redirect to success page or quote view
        return redirect()->route('quotes.view', $this->quote);
    }

    private function extractBase64FromDataUrl(string $dataUrl): string
    {
        // Remove data URL prefix (e.g., "data:image/png;base64,")
        if (str_contains($dataUrl, 'base64,')) {
            return explode('base64,', $dataUrl)[1];
        }

        return $dataUrl;
    }

    public function clearSignature()
    {
        $this->signatureData = null;
    }
}
