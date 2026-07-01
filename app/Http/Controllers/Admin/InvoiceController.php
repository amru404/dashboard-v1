<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with('users')
            ->latest()
            ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load('users');

        return view('admin.invoices.show', compact('invoice'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.invoices.create', compact('users'));
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
            $path = $file->store('invoices', 'private');
            $fileData = [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        $invoice = Invoice::create(array_merge($validated, $fileData, [
            'is_active' => $request->boolean('is_active'),
        ]));

        $invoice->users()->sync($request->user_ids);

        return redirect()->route('admin.invoices.index')
            ->with('status', 'Invoice created.');
    }

    public function edit(Invoice $invoice): View
    {
        $users = User::orderBy('name')->get();
        $invoice->load('users');

        return view('admin.invoices.edit', compact('invoice', 'users'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
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
            if ($invoice->file_path) {
                Storage::disk('private')->delete($invoice->file_path);
            }
            $file = $request->file('file');
            $path = $file->store('invoices', 'private');
            $validated = array_merge($validated, [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        $invoice->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        $invoice->users()->sync($request->user_ids);

        return redirect()->route('admin.invoices.index')
            ->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->file_path) {
            Storage::disk('private')->delete($invoice->file_path);
        }
        $invoice->users()->detach();
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('status', 'Invoice deleted.');
    }

    public function download(Invoice $invoice)
    {
        abort_if(! $invoice->file_path, 404);

        return Storage::disk('private')->download(
            $invoice->file_path,
            $invoice->original_filename
        );
    }
}
