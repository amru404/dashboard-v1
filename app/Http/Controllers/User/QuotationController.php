<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = auth()->user()
            ->quotations()
            ->where('is_active', true)
            ->latest()
            ->paginate(20);

        return view('user.quotations.index', compact('quotations'));
    }

    public function download(Quotation $quotation)
    {
        // Check if user has access to this quotation
        abort_if(!auth()->user()->quotations->contains($quotation), 403);

        // Check if download has expired
        if ($quotation->download_expired_at && $quotation->download_expired_at->isPast()) {
            return redirect()->route('user.quotations.index')
                ->with('error', 'Download link has expired.');
        }

        abort_if(!$quotation->file_path, 404);

        return Storage::disk('private')->download(
            $quotation->file_path,
            $quotation->original_filename
        );
    }
}
