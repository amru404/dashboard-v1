@extends('layouts.user')

@section('title', 'License Details')

@section('content')
    @php
        $daysUntilExpiry = $license->daysUntilExpiry();
        $productPath = $license->product->getCatalogPath();
        $subProductPath = $license->subProduct?->getCatalogPath();
    @endphp

    <x-page-header title="License details" subtitle="{{ $license->product->name }} - {{ $license->licenseType->name }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('user.licenses.index')">Back to licenses</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Product</dt>
                    <dd class="mt-1 text-base font-semibold text-madani-deep">{{ $license->product->name }}</dd>
                    <dd class="mt-1 text-sm text-madani-muted">{{ $productPath }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Sub-product</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->subProduct?->name ?? 'None' }}</dd>
                    @if ($subProductPath)
                        <dd class="mt-1 text-sm text-madani-muted">{{ $subProductPath }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">License type</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->licenseType->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Expiry</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</dd>
                    <dd class="mt-2 text-sm text-madani-muted">
                        @if ($license->isExpired())
                            Expired
                        @elseif ($daysUntilExpiry !== null)
                            {{ $daysUntilExpiry }} days remaining
                        @else
                            No expiry
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Max activations</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->max_activations ?? 'Unlimited' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Active activations</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->activeActivationCount() }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-madani-muted">License key</dt>
                    <dd class="mt-2">
                        <x-license-key-display :license="$license" :reveal-url="route('user.licenses.show-key', $license)" />
                    </dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h2 class="text-lg font-bold text-madani-deep">Activation records</h2>
            <div class="mt-5 space-y-3">
                @forelse ($license->activations as $activation)
                    <div class="rounded-xl border border-madani-border bg-madani-ghost p-4">
                        <p class="font-mono text-sm font-semibold text-madani-deep">{{ $activation->device_id }}</p>
                        <p class="mt-1 text-sm text-madani-muted">{{ $activation->hostname ?? 'No hostname' }} - {{ ucfirst($activation->status) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-madani-muted">No activations recorded.</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <div id="reveal-key-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-madani-depth/70 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-madani-deep">Plaintext license key</h2>
                    <p class="mt-2 text-sm leading-6 text-madani-muted">Use this key only for your licensed installer.</p>
                </div>
                <button id="close-reveal-modal" type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-madani-muted hover:bg-madani-ghost">Close</button>
            </div>

            <div id="reveal-key-message" class="mt-5 rounded-xl border border-madani-border bg-madani-ghost px-4 py-3 font-mono text-sm font-semibold text-madani-deep">
                Loading...
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button id="copy-revealed-key" type="button" class="inline-flex items-center justify-center rounded-lg bg-madani-success px-5 py-3 text-sm font-semibold text-white transition hover:bg-madani-green">Copy key</button>
                <button id="close-reveal-modal-secondary" type="button" class="inline-flex items-center justify-center rounded-lg border border-madani-deep px-5 py-3 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white">Done</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const button = document.getElementById('reveal-license-key');
            const modal = document.getElementById('reveal-key-modal');
            const message = document.getElementById('reveal-key-message');
            const closeButtons = [
                document.getElementById('close-reveal-modal'),
                document.getElementById('close-reveal-modal-secondary'),
            ];
            const copyButton = document.getElementById('copy-revealed-key');
            let revealedKey = '';

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                revealedKey = '';
                message.textContent = 'Loading...';
            };

            closeButtons.forEach((closeButton) => closeButton?.addEventListener('click', closeModal));

            button?.addEventListener('click', async () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                message.textContent = 'Loading...';

                try {
                    const response = await fetch(button.dataset.url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();

                    if (! response.ok) {
                        throw new Error(data.message || 'Unable to reveal this key.');
                    }

                    revealedKey = data.license_key;
                    message.textContent = revealedKey;
                } catch (error) {
                    revealedKey = '';
                    message.textContent = error.message;
                }
            });

            copyButton?.addEventListener('click', async () => {
                if (! revealedKey || ! navigator.clipboard) {
                    return;
                }

                await navigator.clipboard.writeText(revealedKey);
                copyButton.textContent = 'Copied';
            });
        })();
    </script>
@endsection
