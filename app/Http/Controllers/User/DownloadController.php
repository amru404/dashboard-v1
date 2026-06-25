<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DownloadItem;
use App\Models\DownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class DownloadController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $downloadItems = DownloadItem::query()
            ->with('product')
            ->availableForUser($request->user())
            ->when($filters['product_id'] ?? null, fn ($query, $productId) => $query->where('product_id', $productId))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhere('file_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $filterProducts = \App\Models\Product::query()
            ->whereIn('id', DownloadItem::query()
                ->availableForUser($request->user())
                ->pluck('product_id')
                ->unique()
            )
            ->orderBy('name')
            ->get();

        return view('user.downloads.index', [
            'downloadItems' => $downloadItems,
            'filters' => $filters,
            'filterProducts' => $filterProducts,
        ]);
    }

    public function download(Request $request, DownloadItem $downloadItem): StreamedResponse
    {
        $user = $request->user();

        $allowed = DownloadItem::query()
            ->availableForUser($user)
            ->whereKey($downloadItem->id)
            ->exists();

        abort_unless($allowed, 404);
        abort_unless(Storage::disk('local')->exists($downloadItem->file_path), 404);

        DownloadLog::logDownload($user->id, $downloadItem->id, $request->ip());

        return Storage::disk('local')->download($downloadItem->file_path, $downloadItem->file_name);
    }
}
