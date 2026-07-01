<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::with('users')
            ->latest()
            ->paginate(20);

        return view('admin.quotations.index', compact('quotations'));
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load('users');

        return view('admin.quotations.show', compact('quotation'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.quotations.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'file' => [
                'nullable', 'file', 'max:51200',
                'mimes:pdf,doc,docx,xls,xlsx,zip',
            ],
            'status' => ['required', 'in:draft,sent,paid,cancelled'],
            'download_expired_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $fileData = [];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('quotations', 'private');
            $fileData = [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        $quotation = Quotation::create(array_merge($validated, $fileData, [
            'is_active' => $request->boolean('is_active'),
        ]));

        $quotation->users()->sync($request->user_ids);

        return redirect()->route('admin.quotations.index')
            ->with('status', 'Quotation created.');
    }

    public function edit(Quotation $quotation): View
    {
        $users = User::orderBy('name')->get();
        $quotation->load('users');

        return view('admin.quotations.edit', compact('quotation', 'users'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'file' => [
                'nullable', 'file', 'max:512000',
                'mimes:pdf,doc,docx,xls,xlsx,zip',
            ],
            'status' => ['required', 'in:draft,sent,paid,cancelled'],
            'download_expired_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('file')) {
            if ($quotation->file_path) {
                Storage::disk('private')->delete($quotation->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('quotations', 'private');
            $validated = array_merge($validated, [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        $quotation->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        $quotation->users()->sync($request->user_ids);

        return redirect()->route('admin.quotations.index')
            ->with('status', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        if ($quotation->file_path) {
            Storage::disk('private')->delete($quotation->file_path);
        }
        $quotation->users()->detach();
        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('status', 'Quotation deleted.');
    }

    public function download(Quotation $quotation)
    {
        abort_if(! $quotation->file_path, 404);

        return Storage::disk('private')->download(
            $quotation->file_path,
            $quotation->original_filename
        );
    }
}
