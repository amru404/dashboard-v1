<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = auth()->user()
            ->invoices()
            ->where('is_active', true)
            ->latest()
            ->paginate(20);

        return view('user.invoices.index', compact('invoices'));
    }

    public function download(Invoice $invoice)
    {
        // Check if user has access to this invoice
        abort_if(!auth()->user()->invoices->contains($invoice), 403);

        // Check if download has expired
        if ($invoice->download_expired_at && $invoice->download_expired_at->isPast()) {
            return redirect()->route('user.invoices.index')
                ->with('error', 'Download link has expired.');
        }

        abort_if(!$invoice->file_path, 404);

        return Storage::disk('private')->download(
            $invoice->file_path,
            $invoice->original_filename
        );
    }
}
