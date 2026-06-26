@extends('layouts.admin')

@section('title', 'License Types')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">License Types</h1>
            <p class="text-base text-gray-300">
                License categories used when issuing installer-facing license records.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('admin.license-types.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Create Type
            </a>
        </div>
    </div>
</div>

<div class="vd-card  border-[#2a3f5f] overflow-hidden !p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-[#0f1829]/30 border-b border-[#2a3f5f]">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Licenses</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2a3f5f]">
                @forelse ($licenseTypes as $licenseType)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-white">{{ $licenseType->name }}</p>
                            <p class="mt-1 font-mono text-sm text-gray-400">{{ $licenseType->code ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $licenseType->licenses_count }}</td>
                        <td class="px-6 py-4">
                            @if ($licenseType->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.license-types.show', $licenseType) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">View</a>
                                <a href="{{ route('admin.license-types.edit', $licenseType) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.license-types.destroy', $licenseType) }}"
                                      onsubmit="return confirm('Delete this license type?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-400 hover:text-red-300 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                            No license types have been created.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-[#2a3f5f] px-6 py-4">
        {{ $licenseTypes->links() }}
    </div>
</div>
@endsection
