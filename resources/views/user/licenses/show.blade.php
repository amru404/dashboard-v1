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
    <div class="vd-card border-[#2a3f5f] !p-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">License Details</h1>
                <p class="text-sm text-gray-400 font-mono">{{ $license->licenseType->name }}</p>
            </div>
            <a href="{{ route('user.licenses.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                ← Back
            </a>
        </div>

        {{-- Quick Stats --}}
        <div class="flex flex-wrap gap-3">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-vd-primary/10 border border-vd-primary/20">
                <span class="text-xs text-gray-400">Product:</span>
                <span class="text-sm font-bold text-white">{{ $license->product->name }}</span>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20">
                <span class="text-xs text-gray-400">Activations:</span>
                <span class="text-sm font-bold text-blue-400">{{ $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }}</span>
            </div>
            @if ($license->isExpired())
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                    Expired
                </span>
            @elseif ($daysUntilExpiry !== null)
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                    {{ $daysUntilExpiry }} days left
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                    No expiry
                </span>
            @endif
        </div>
    </div>
</div>

{{-- ── Two Column Layout ── --}}
<div class="grid gap-6 lg:grid-cols-3">

    {{-- Left: License Information ── --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- License Info Card --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-xl font-bold text-white mb-5">License Information</h2>
            
            <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Product</dt>
                    <dd class="text-white font-medium">{{ $license->product->name }}</dd>
                    @if ($productPath)
                        <dd class="text-xs text-gray-500 mt-1">{{ $productPath }}</dd>
                    @endif
                </div>
                
                @if ($license->subProduct)
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Sub-product</dt>
                    <dd class="text-white font-medium">{{ $license->subProduct->name }}</dd>
                    @if ($subProductPath)
                        <dd class="text-xs text-gray-500 mt-1">{{ $subProductPath }}</dd>
                    @endif
                </div>
                @endif
                
                <div>
                    <dt class="text-xs text-gray-400 mb-1">License Type</dt>
                    <dd class="text-white font-medium">{{ $license->licenseType->name }}</dd>
                </div>
                
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Expiry Date</dt>
                    <dd class="text-white font-medium">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</dd>
                </div>
                
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Max Activations</dt>
                    <dd class="text-white font-medium">{{ $license->max_activations ?? 'Unlimited' }}</dd>
                </div>
                
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Active Activations</dt>
                    <dd class="text-cyan-400 font-bold">{{ $license->activeActivationCount() }}</dd>
                </div>

                @if ($license->quantity)
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Quantity</dt>
                    <dd class="text-white font-medium">{{ $license->quantity }}</dd>
                </div>
                @endif

                @if ($license->client_name)
                <div>
                    <dt class="text-xs text-gray-400 mb-1">Client Name</dt>
                    <dd class="text-white font-medium">{{ $license->client_name }}</dd>
                </div>
                @endif
            </dl>

            @if (! $license->is_parent_only)
                <div class="mt-6 pt-6 border-t border-[#2a3f5f]">
                    <h3 class="text-sm font-semibold text-white mb-3">License Key</h3>
                    <x-license-key-display
                        :license="$license"
                        :reveal-url="route('user.licenses.show-key', $license)" />
                </div>
            @endif
        </div>

        {{-- Activation Records --}}
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-xl font-bold text-white mb-5">Activation History</h2>
            
            <div class="space-y-3">
                @forelse ($license->activations as $activation)
                    <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-mono text-sm text-white mb-1 break-all">{{ $activation->device_id }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $activation->hostname ?? 'Unknown device' }}
                                </p>
                            </div>
                            @if ($activation->status === 'active')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">
                                    {{ ucfirst($activation->status) }}
                                </span>
                            @endif
                        </div>
                        
                        @if ($activation->location)
                            <p class="text-xs text-gray-500 mt-2">
                                <svg class="inline w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $activation->location }}
                            </p>
                        @endif

                        @if ($activation->activated_at)
                            <p class="text-xs text-gray-500 mt-1">
                                Activated: {{ $activation->activated_at->format('M j, Y H:i') }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No activations recorded</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Quick Actions & Stats ── --}}
    <div class="lg:col-span-1">
        <div class="vd-card border-[#2a3f5f] !p-6">
            <h2 class="text-lg font-bold text-white mb-5">Summary</h2>
            
            <div class="space-y-4">
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">Status</dt>
                    <dd>
                        @if ($license->isExpired())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                                Expired
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">
                                Active
                            </span>
                        @endif
                    </dd>
                </div>

                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">Total Activations</dt>
                    <dd class="text-2xl font-bold text-vd-primary">{{ $license->activations->count() }}</dd>
                </div>

                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">Active Now</dt>
                    <dd class="text-2xl font-bold text-green-400">{{ $license->activeActivationCount() }}</dd>
                </div>

                @if ($daysUntilExpiry !== null)
                <div class="rounded-lg border border-[#2a3f5f] bg-[#0f1829]/40 p-4">
                    <dt class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">Days Remaining</dt>
                    <dd class="text-2xl font-bold text-orange-400">{{ $daysUntilExpiry }}</dd>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Reveal Key Modal ── --}}
<div id="reveal-key-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-[#0a0f1a]/90 px-4 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-xl border border-[#2a3f5f] bg-[#091729] p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h2 class="text-xl font-bold text-white">License Key</h2>
                <p class="mt-1 text-sm text-gray-400">Use this key only for your licensed installer</p>
            </div>
            <button id="close-reveal-modal" type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 hover:bg-white/15 text-gray-300 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="reveal-key-message"
             class="rounded-lg border border-[#2a3f5f] bg-[#0f1829] px-4 py-3 font-mono text-sm text-white break-all mb-5">
            Loading…
        </div>

        <div class="flex gap-3">
            <button id="copy-revealed-key" type="button" class="flex-1 inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Copy Key
            </button>
            <button id="close-reveal-modal-secondary" type="button" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                Close
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
            const originalText = copyButton.innerHTML;
            copyButton.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Copied!';
            setTimeout(() => copyButton.innerHTML = originalText, 2000);
        });
    })();
</script>

@endsection
