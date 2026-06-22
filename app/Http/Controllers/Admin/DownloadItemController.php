<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadItem;
use Illuminate\View\View;

class DownloadItemController extends Controller
{
    public function index(): View
    {
        return view('admin.download-items.index', [
            'downloadItems' => DownloadItem::query()
                ->with(['product', 'user.organization'])
                ->withCount('logs')
                ->latest()
                ->paginate(20),
        ]);
    }
}
