<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadItem;
use App\Models\User;
use App\Support\ProductTreeBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DownloadController extends Controller
{
    public function __construct(private readonly ProductTreeBuilder $productTreeBuilder)
    {
    }

    public function index(): View
    {
        return view('admin.downloads.index', [
            'downloadItems' => DownloadItem::query()
                ->with(['product', 'user.organization'])
                ->withCount('logs')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.downloads.create', [
            'downloadItem' => new DownloadItem(['is_active' => true]),
            ...$this->formData(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request, uploadOrPathRequired: true);
        $data = $this->applyUploadedFile($request, $data);

        $downloadItem = DownloadItem::query()->create($data);

        return redirect()
            ->route('admin.download-items.show', $downloadItem)
            ->with('status', 'Download item created.');
    }

    public function show(DownloadItem $downloadItem): View
    {
        $downloadItem->load(['product.parent', 'user.organization', 'logs.user.organization'])
            ->loadCount('logs');

        return view('admin.downloads.show', [
            'downloadItem' => $downloadItem,
        ]);
    }

    public function edit(DownloadItem $downloadItem): View
    {
        return view('admin.downloads.edit', [
            'downloadItem' => $downloadItem,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, DownloadItem $downloadItem): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->applyUploadedFile($request, $data, $downloadItem);

        foreach (['file_name', 'file_path', 'file_size'] as $optionalExistingField) {
            if (
                array_key_exists($optionalExistingField, $data) &&
                ($data[$optionalExistingField] === null || $data[$optionalExistingField] === '')
            ) {
                unset($data[$optionalExistingField]);
            }
        }

        $downloadItem->update($data);

        return redirect()
            ->route('admin.download-items.show', $downloadItem)
            ->with('status', 'Download item updated.');
    }

    public function destroy(DownloadItem $downloadItem): RedirectResponse
    {
        $downloadItem->delete();

        return redirect()
            ->route('admin.download-items.index')
            ->with('status', 'Download item deleted. Private files are left in storage for manual retention review.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, bool $uploadOrPathRequired = false): array
    {
        $rules = [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_USER)),
            ],
            'file_name' => ['nullable', 'string', 'max:255'],
            'file_upload' => ['nullable', 'file'],
            'file_path' => ['nullable', 'string', 'max:255', $this->privateDownloadsPathRule()],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'version' => ['nullable', 'string', 'max:100'],
            'expired_date' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        if ($uploadOrPathRequired) {
            $rules['file_upload'][] = 'required_without:file_path';
            $rules['file_path'][] = 'required_without:file_upload';
        }

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active');

        if (! $request->hasFile('file_upload') && blank($data['file_path'] ?? null)) {
            unset($data['file_path']);
        }

        unset($data['file_upload']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function applyUploadedFile(Request $request, array $data, ?DownloadItem $downloadItem = null): array
    {
        if (! $request->hasFile('file_upload')) {
            if (blank($data['file_name'] ?? null) && blank($data['file_path'] ?? null) && ! $downloadItem) {
                throw ValidationException::withMessages([
                    'file_name' => 'Provide a file name or upload a file.',
                ]);
            }

            if (blank($data['file_name'] ?? null) && filled($data['file_path'] ?? null)) {
                $data['file_name'] = basename((string) $data['file_path']);
            }

            return $data;
        }

        $file = $request->file('file_upload');
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeBaseName = Str::slug($baseName) ?: 'download';
        $fileName = $safeBaseName.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(8));

        if ($extension !== '') {
            $fileName .= '.'.$extension;
        }

        $path = $file->storeAs('downloads', $fileName, 'local');

        if (! $path) {
            throw ValidationException::withMessages([
                'file_upload' => 'The file could not be stored in private downloads.',
            ]);
        }

        $data['file_path'] = $path;
        $data['file_name'] = filled($data['file_name'] ?? null)
            ? $data['file_name']
            : $file->getClientOriginalName();
        $data['file_size'] = $file->getSize();

        return $data;
    }

    private function privateDownloadsPathRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $path = str_replace('\\', '/', (string) $value);

            if (
                str_starts_with($path, '/') ||
                str_contains($path, '..') ||
                ! str_starts_with($path, 'downloads/') ||
                str_contains($path, 'public/')
            ) {
                $fail('Download files must use a relative private path under downloads/.');

                return;
            }

            if (! Storage::disk('local')->exists($path)) {
                $fail('The registered private file path does not exist.');
            }
        };
    }

    /**
     * @return array{users: EloquentCollection<int, User>, productOptions: Collection<int, array{id: int, label: string, path: string, depth: int}>}
     */
    private function formData(): array
    {
        return [
            'users' => User::query()
                ->with('organization')
                ->where('role', User::ROLE_USER)
                ->orderBy('name')
                ->get(),
            'productOptions' => $this->productTreeBuilder->options(),
        ];
    }
}
