@extends('layouts.user')

@section('title', 'My Licenses')

@section('content')
    <x-page-header title="My licenses" subtitle="License records assigned to your account." />

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Key</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Activations</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Expiry</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($licenses as $license)
                        <tr>
                            <td class="px-6 py-4 font-mono text-sm font-semibold text-madani-deep">{{ $license->masked_license_key }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $license->product->name }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $license->subProduct?->name ?? 'No sub-product' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->licenseType->name }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->active_activations_count }} active / {{ $license->max_activations ?? 'unlimited' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-button variant="ghost" :href="route('user.licenses.show', $license)">View</x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-madani-muted">No licenses are assigned to your account.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-madani-border px-6 py-4">
            {{ $licenses->links() }}
        </div>
    </x-card>
@endsection
