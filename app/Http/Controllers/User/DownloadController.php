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
        return view('user.downloads.index', [
            'downloadItems' => DownloadItem::query()
                ->with('product')
                ->availableForUser($request->user())
                ->latest()
                ->paginate(12),
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
