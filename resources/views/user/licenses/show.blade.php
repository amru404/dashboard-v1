@extends('layouts.user')

@section('title', 'License Details')

@section('content')
    @php
        $daysUntilExpiry = $license->daysUntilExpiry();
        $productPath     = $license->product->getCatalogPath();
        $subProductPath  = $license->subProduct?->getCatalogPath();
    @endphp

    <x-page-header
        title="License Details"
        subtitle="{{ $license->product->name }} — {{ $license->licenseType->name }}"
    >
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('user.licenses.index')">Back to Licenses</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.65fr]">

        {{-- Main license info --}}
        <div class="vd-card">
            <h2 class="text-label-lg text-vd-on-surface mb-5">License Information</h2>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-label-sm text-vd-muted">Product</dt>
                    <dd class="mt-1 text-label-md text-vd-on-surface">{{ $license->product->name }}</dd>
                    @if ($productPath)
                        <dd class="mt-1 text-body-sm text-vd-muted">{{ $productPath }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-label-sm text-vd-muted">Sub-product</dt>
                    <dd class="mt-1 text-label-md text-vd-on-surface">{{ $license->subProduct?->name ?? '—' }}</dd>
                    @if ($subProductPath)
                        <dd class="mt-1 text-body-sm text-vd-muted">{{ $subProductPath }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-label-sm text-vd-muted">License Type</dt>
                    <dd class="mt-1 text-label-md text-vd-on-surface">{{ $license->licenseType->name }}</dd>
                </div>
                <div>
                    <dt class="text-label-sm text-vd-muted">Expiry</dt>
                    <dd class="mt-1 text-label-md text-vd-on-surface">
                        {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                    </dd>
                    <dd class="mt-1">
                        @if ($license->isExpired())
                            <span class="vd-chip-error">Expired</span>
                        @elseif ($daysUntilExpiry !== null)
                            <span class="vd-chip-warning">{{ $daysUntilExpiry }} days remaining</span>
                        @else
                            <span class="vd-chip-success">No expiry</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-label-sm text-vd-muted">Max Activations</dt>
                    <dd class="mt-1 text-label-md text-vd-on-surface">{{ $license->max_activations ?? 'Unlimited' }}</dd>
                </div>
                <div>
                    <dt class="text-label-sm text-vd-muted">Active Activations</dt>
                    <dd class="mt-1 text-label-md text-vd-on-surface">{{ $license->activeActivationCount() }}</dd>
                </div>
                @if (! $license->is_parent_only)
                    <div class="sm:col-span-2">
                        <dt class="text-label-sm text-vd-muted mb-2">License Key</dt>
                        <dd>
                            <x-license-key-display
                                :license="$license"
                                :reveal-url="route('user.licenses.show-key', $license)" />
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Activations --}}
        <div class="vd-card">
            <h2 class="text-label-lg text-vd-on-surface mb-5">Activation Records</h2>
            <div class="space-y-3">
                @forelse ($license->activations as $activation)
                    <div class="rounded-lg border border-vd-border bg-vd-secondary/40 p-4">
                        <p class="font-mono text-body-sm text-vd-on-surface">{{ $activation->device_id }}</p>
                        <p class="mt-1 text-body-sm text-vd-muted">
                            {{ $activation->hostname ?? 'No hostname' }}
                            &mdash;
                            <span class="{{ $activation->status === 'active' ? 'text-vd-success' : 'text-vd-muted' }}">
                                {{ ucfirst($activation->status) }}
                            </span>
                        </p>
                        @if ($activation->location)
                            <p class="mt-1 text-body-sm text-vd-muted">{{ $activation->location }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-body-sm text-vd-muted">No activations recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Reveal key modal --}}
    <div id="reveal-key-modal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-vd-neutral/80 px-4 backdrop-blur-sm">
        <div class="w-full max-w-lg rounded-xl border border-vd-border bg-vd-surface p-6 shadow-vd-lg">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-headline-sm text-vd-on-surface">Plaintext License Key</h2>
                    <p class="mt-1 text-body-sm text-vd-muted leading-relaxed">
                        Use this key only for your licensed installer.
                    </p>
                </div>
                <button id="close-reveal-modal" type="button" class="vd-btn-ghost">Close</button>
            </div>

            <div id="reveal-key-message"
                 class="rounded-lg border border-vd-border-strong bg-vd-secondary px-4 py-3 font-mono text-body-sm text-vd-on-surface break-all">
                Loading…
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <button id="copy-revealed-key" type="button" class="vd-btn-primary">Copy Key</button>
                <button id="close-reveal-modal-secondary" type="button" class="vd-btn-secondary">Done</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const button     = document.getElementById('reveal-license-key');
            const modal      = document.getElementById('reveal-key-modal');
            const message    = document.getElementById('reveal-key-message');
            const copyButton = document.getElementById('copy-revealed-key');
            const closeBtns  = [
                document.getElementById('close-reveal-modal'),
                document.getElementById('close-reveal-modal-secondary'),
            ];
            let revealedKey = '';

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                revealedKey = '';
                message.textContent = 'Loading…';
            };

            closeBtns.forEach(b => b?.addEventListener('click', closeModal));

            button?.addEventListener('click', async () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                message.textContent = 'Loading…';
                try {
                    const resp = await fetch(button.dataset.url, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await resp.json();
                    if (!resp.ok) throw new Error(data.message || 'Unable to reveal key.');
                    revealedKey = data.license_key;
                    message.textContent = revealedKey;
                } catch (e) {
                    revealedKey = '';
                    message.textContent = e.message;
                }
            });

            copyButton?.addEventListener('click', async () => {
                if (!revealedKey || !navigator.clipboard) return;
                await navigator.clipboard.writeText(revealedKey);
                copyButton.textContent = 'Copied!';
                setTimeout(() => copyButton.textContent = 'Copy Key', 2000);
            });
        })();
    </script>
@endsection
