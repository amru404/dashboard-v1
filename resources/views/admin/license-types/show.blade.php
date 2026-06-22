@extends('layouts.admin')

@section('title', $licenseType->name)

@section('content')
    <x-page-header title="{{ $licenseType->name }}" subtitle="License type configuration and usage.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.license-types.edit', $licenseType)">Edit type</x-button>
            <x-button variant="secondary" :href="route('admin.license-types.index')">Back to types</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Code</dt>
                    <dd class="mt-1 text-base font-bold text-madani-deep">{{ $licenseType->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Status</dt>
                    <dd class="mt-1"><x-badge :active="$licenseType->is_active" /></dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Created</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $licenseType->created_at?->format('M j, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Updated</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $licenseType->updated_at?->format('M j, Y') }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Assigned licenses</p>
            <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $licenseType->licenses_count }}</p>
            <p class="mt-2 text-sm leading-6 text-madani-muted">Types assigned to licenses are protected from deletion.</p>

            <form method="POST" action="{{ route('admin.license-types.destroy', $licenseType) }}" class="mt-6" onsubmit="return confirm('Delete this license type?')">
                @csrf
                @method('DELETE')
                <x-button variant="danger">Delete license type</x-button>
            </form>
        </x-card>
    </div>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="border-b border-madani-border bg-madani-ghost px-6 py-5">
            <h2 class="text-lg font-bold text-madani-deep">Recent licenses</h2>
            <p class="mt-1 text-sm text-madani-muted">Latest licenses using this type.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Key</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($licenseType->licenses as $license)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $license->user->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $license->user->organization?->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-deep">{{ $license->product->name }}</td>
                            <td class="px-6 py-4 font-mono text-sm text-madani-muted">{{ $license->masked_license_key }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-button variant="ghost" :href="route('admin.licenses.show', $license)">View</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-madani-muted">
                                No licenses are using this type yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
