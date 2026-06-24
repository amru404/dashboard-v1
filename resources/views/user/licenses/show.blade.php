@extends('layouts.user')

@section('title', 'License Details')

@section('content')
    @php
        $daysUntilExpiry = $license->daysUntilExpiry();
        $productPath     = $license->product->getCatalogPath();
        $subProductPath  = $license->subProduct?->getCatalogPath();
    @endphp

{{-- ── Page Header ── --}}
<div class="mb-6">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">License Details</h1>
            <p class="text-base text-gray-300">{{ $license->product->name }} — {{ $license->licenseType->name }}</p>
        </div>
        <a href="{{ route('user.licenses.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
            ← Back to Licenses
        </a>
    </div>
</div>

{{-- ── Two Column Layout: License Info + Activation Records ── --}}
<div class="grid gap-6 lg:grid-cols-[1fr_0.65fr]">

    {{-- Left: License Information ── --}}
    <div class="vd-card  border-[#2a3f5f] !p-6">
        <h2 class="text-xl font-bold text-white mb-5">License Information</h2>
        <dl class="grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2">Product</dt>
                <dd class="text-base font-semibold text-white">{{ $license->product->name }}</dd>
                @if ($productPath)
                    <dd class="mt-1 text-sm text-gray-400">{{ $productPath }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2">Sub-product</dt>
                <dd class="text-base font-semibold text-white">{{ $license->subProduct?->name ?? '—' }}</dd>
                @if ($subProductPath)
                    <dd class="mt-1 text-sm text-gray-400">{{ $subProductPath }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2">License Type</dt>
                <dd class="text-base font-semibold text-white">{{ $license->licenseType->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2">Expiry</dt>
                <dd class="text-base font-semibold text-white mb-2">
                    {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
                </dd>
                <dd>
                    @if ($license->isExpired())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                            Expired
                        </span>
                    @elseif ($daysUntilExpiry !== null)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                            {{ $daysUntilExpiry }} days remaining
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                            No expiry
                        </span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2">Max Activations</dt>
                <dd class="text-base font-semibold text-white">{{ $license->max_activations ?? 'Unlimited' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2">Active Activations</dt>
                <dd class="text-base font-semibold text-cyan-400">{{ $license->activeActivationCount() }}</dd>
            </div>
            @if (! $license->is_parent_only)
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-3">License Key</dt>
                    <dd>
                        <x-license-key-display
                            :license="$license"
                            :reveal-url="route('user.licenses.show-key', $license)" />
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Right: Activation Records ── --}}
    <div class="vd-card  border-[#2a3f5f] !p-6">
        <h2 class="text-xl font-bold text-white mb-5">Activation Records</h2>
        <div class="space-y-3">
            @forelse ($license->activations as $activation)
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <p class="font-mono text-sm text-white mb-1">{{ $activation->device_id }}</p>
                    <p class="text-sm text-gray-400">
                        {{ $activation->hostname ?? 'No hostname' }}
                        <span class="mx-2">·</span>
                        <span class="{{ $activation->status === 'active' ? 'text-green-400 font-semibold' : 'text-gray-400' }}">
                            {{ ucfirst($activation->status) }}
                        </span>
                    </p>
                    @if ($activation->location)
                        <p class="mt-1 text-sm text-gray-400">{{ $activation->location }}</p>
                    @endif
                </div>
            @empty
                <div class="text-center py-6">
                    <svg class="mx-auto mb-3 h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <p class="text-sm text-gray-400">No activations recorded.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Reveal Key Modal ── --}}
<div id="reveal-key-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0a0f1a]/90 px-4 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-xl border border-[#2a3f5f]  p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h2 class="text-xl font-bold text-white">Plaintext License Key</h2>
                <p class="mt-1 text-sm text-gray-300 leading-relaxed">
                    Use this key only for your licensed installer.
                </p>
            </div>
            <button id="close-reveal-modal" type="button" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-gray-300 hover:text-white transition-colors">
                Close
            </button>
        </div>

        <div id="reveal-key-message"
             class="rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 font-mono text-sm text-white break-all mb-5">
            Loading…
        </div>

        <div class="flex flex-wrap gap-3">
            <button id="copy-revealed-key" type="button" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                Copy Key
            </button>
            <button id="close-reveal-modal-secondary" type="button" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                Done
            </button>
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
