<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\SalesContent;
use App\Models\PlaybookExecution;
use App\Models\PlaybookStep;
use App\Models\QuoteSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesEnablementController extends Controller
{
    public function previewQuote(Quote $quote)
    {
        $quote->load(['items', 'company', 'contact', 'createdBy', 'signatures']);
        
        return view('sales-enablement.quote-preview', compact('quote'));
    }

    public function downloadQuotePdf(Quote $quote)
    {
        $quote->load(['items', 'company', 'contact', 'createdBy']);
        
        $pdf = Pdf::loadView('sales-enablement.quote-pdf', compact('quote'));
        
        return $pdf->download("quote-{$quote->quote_number}.pdf");
    }

    public function signQuote(Quote $quote)
    {
        // Check if quote is in a signable state
        if (!in_array($quote->status, ['sent', 'viewed'])) {
            abort(403, 'This quote cannot be signed in its current state.');
        }

        // Update quote status to viewed if not already
        if ($quote->status === 'sent') {
            $quote->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);
        }

        return view('sales-enablement.quote-signature', compact('quote'));
    }

    public function saveSignature(Request $request, Quote $quote)
    {
        $request->validate([
            'signature_data' => 'required|string',
            'signer_name' => 'required|string|max:255',
            'signer_email' => 'required|email|max:255',
            'signer_title' => 'nullable|string|max:255',
            'signature_type' => 'required|in:draw,type,upload',
        ]);

        // Create signature record
        QuoteSignature::create([
            'quote_id' => $quote->id,
            'signer_name' => $request->signer_name,
            'signer_email' => $request->signer_email,
            'signer_title' => $request->signer_title,
            'signature_data' => str_replace('data:image/png;base64,', '', $request->signature_data),
            'signature_type' => $request->signature_type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'signed_at' => now(),
        ]);

        // Update quote status
        $quote->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quote signed successfully!',
            'redirect_url' => route('sales-enablement.quotes.preview', $quote),
        ]);
    }

    public function downloadContent(SalesContent $content)
    {
        // Check if file exists
        if (!$content->file_path || !Storage::disk('public')->exists($content->file_path)) {
            abort(404, 'File not found.');
        }

        // Record download analytics
        $content->recordDownload(auth()->user());

        return Storage::disk('public')->download($content->file_path, $content->file_name);
    }

    public function previewContent(SalesContent $content)
    {
        // Record view analytics
        $content->recordView(auth()->user());

        return view('sales-enablement.content-preview', compact('content'));
    }

    public function executePlaybook(PlaybookExecution $execution)
    {
        // Ensure user owns this execution
        if ($execution->user_id !== auth()->id()) {
            abort(403);
        }

        $execution->load(['playbook.steps' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }, 'deal', 'contact']);

        $currentStep = $execution->getCurrentStep();
        $progress = $execution->getProgressPercentage();

        return view('sales-enablement.playbook-execution', compact(
            'execution',
            'currentStep',
            'progress'
        ));
    }

    public function completePlaybookStep(Request $request, PlaybookExecution $execution)
    {
        // Ensure user owns this execution
        if ($execution->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'step_id' => 'required|exists:playbook_steps,id',
            'result' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $step = PlaybookStep::findOrFail($request->step_id);
        
        // Ensure step belongs to this playbook
        if ($step->playbook_id !== $execution->playbook_id) {
            abort(403);
        }

        $result = [
            'notes' => $request->notes,
            'completed_by' => auth()->user()->name,
            'data' => $request->result ?? [],
        ];

        $execution->completeStep($step, $result);

        if ($execution->isCompleted()) {
            return response()->json([
                'success' => true,
                'message' => 'Playbook completed successfully!',
                'completed' => true,
                'redirect_url' => route('sales-enablement.playbooks'),
            ]);
        }

        $nextStep = $execution->getCurrentStep();
        
        return response()->json([
            'success' => true,
            'message' => 'Step completed successfully!',
            'completed' => false,
            'next_step' => $nextStep ? [
                'id' => $nextStep->id,
                'title' => $nextStep->title,
                'description' => $nextStep->description,
                'instructions' => $nextStep->instructions,
                'type' => $nextStep->step_type,
                'content' => $nextStep->content,
            ] : null,
            'progress' => $execution->getProgressPercentage(),
        ]);
    }
}