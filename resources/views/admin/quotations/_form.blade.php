@props(['quotation' => null, 'users' => []])

<form method="POST" action="{{ $quotation ? route('admin.quotations.update', $quotation) : route('admin.quotations.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($quotation)
        @method('PUT')
    @endif

    <div class="vd-card border-vd-formBorder !p-6">
        {{-- Display Name --}}
        <div class="mb-6">
            <x-input-label for="display_name" value="Display Name" />
            <x-form-input id="display_name" name="display_name" type="text" value="{{ $quotation?->display_name ?? old('display_name') }}" required class="mt-2" />
            <x-input-error :messages="$errors->get('display_name')" class="mt-2" />
        </div>

        {{-- Assign Users --}}
        <div class="mb-6">
            <x-input-label value="Assign Users" />
            <div class="space-y-2 max-h-[300px] overflow-y-auto border border-vd-formBorder rounded-lg p-3 bg-black/20 mt-2">
                @forelse ($users as $user)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-white/5 p-2 rounded transition-colors">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                               @checked($quotation && $quotation->users->contains($user))
                               class="w-4 h-4 rounded bg-vd-form border-vd-formBorder text-vd-primary focus:ring-vd-primary focus:ring-offset-0" />
                        <span class="text-sm text-gray-300">{{ $user->name }} <span class="text-xs text-gray-500">({{ $user->email }})</span></span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">No users available</p>
                @endforelse
            </div>
            <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
        </div>

        {{-- File Upload --}}
        <div class="mb-6">
            <x-input-label for="file" value="File Upload" />
            <x-form-input id="file" name="file" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" class="mt-2 file:bg-vd-primary file:text-white file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:cursor-pointer" />
            <p class="mt-2 text-xs text-gray-500">Max 500MB. Accepted: PDF, DOC, DOCX, XLS, XLSX, ZIP</p>
            @if ($quotation?->file_path)
                <p class="mt-2 text-xs text-gray-400">Current file: <span class="text-vd-primary">{{ $quotation->original_filename }}</span></p>
            @endif
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
        </div>

        {{-- Status --}}
        <div class="grid gap-6 sm:grid-cols-2 mb-6">
            <div>
                <x-input-label for="status" value="Status" />
                <select id="status" name="status" required class="vd-input mt-2">
                    <option value="draft" @selected($quotation?->status === 'draft' || old('status') === 'draft')>Draft</option>
                    <option value="sent" @selected($quotation?->status === 'sent' || old('status') === 'sent')>Sent</option>
                    <option value="paid" @selected($quotation?->status === 'paid' || old('status') === 'paid')>Paid</option>
                    <option value="cancelled" @selected($quotation?->status === 'cancelled' || old('status') === 'cancelled')>Cancelled</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            {{-- Download Expired Date --}}
            <div>
                <x-input-label for="download_expired_at" value="Download Expires" />
                <x-form-input id="download_expired_at" name="download_expired_at" type="date" value="{{ $quotation?->download_expired_at?->format('Y-m-d') ?? old('download_expired_at') }}" class="mt-2 color-scheme-dark" />
                <x-input-error :messages="$errors->get('download_expired_at')" class="mt-2" />
            </div>
        </div>

        {{-- Active Toggle --}}
        <div class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked($quotation?->is_active ?? true)
                   class="w-4 h-4 rounded bg-vd-form border-vd-formBorder text-vd-primary focus:ring-vd-primary focus:ring-offset-0" />
            <x-input-label for="is_active" value="Active" />
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3">
        <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
            {{ $quotation ? 'Update Quotation' : 'Create Quotation' }}
        </button>
    </div>
</form>
