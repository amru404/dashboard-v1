@extends('layouts.admin')

@section('title', 'License Activations')

@section('content')
    <x-page-header title="License activations" subtitle="Registered customer devices for issued licenses." />

    <x-card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-madani-ghost">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Device</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-white">
                    @forelse ($activations as $activation)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-madani-deep">{{ $activation->device_id }}</p>
                                <p class="mt-1 text-sm text-madani-muted">{{ $activation->hostname ?? 'No hostname' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">
                                <p class="font-semibold text-madani-deep">{{ $activation->license->user->name }}</p>
                                <p class="mt-1">{{ $activation->license->user->organization?->name ?? 'Unassigned' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $activation->license->product->name }}</td>
                            <td class="px-6 py-4"><x-badge :active="$activation->status === 'active'">{{ ucfirst($activation->status) }}</x-badge></td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.license-activations.destroy', $activation) }}" onsubmit="return confirm('Reset this activation?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger">Reset</x-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-madani-muted">No license activations have been recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-madani-border px-6 py-4">
            {{ $activations->links() }}
        </div>
    </x-card>
@endsection
