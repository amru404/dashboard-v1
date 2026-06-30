@extends('layouts.admin')

@section('title', 'License Details')

@section('content')
    @php
        $daysUntilExpiry = $license->daysUntilExpiry();
        $productPath = $license->product->getCatalogPath();
        $subProductPath = $license->subProduct?->getCatalogPath();
    @endphp

    <x-page-header title="License details" subtitle="{{ $license->user->name }} - {{ $license->product->name }}">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.index')">Back to licenses</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Customer</dt>
                    <dd class="mt-1 text-base font-semibold text-madani-deep">{{ $license->user->name }}</dd>
                    <dd class="mt-1 text-sm text-madani-muted">{{ $license->user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Organization / client</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->client_name ?? $license->user->organization?->name ?? 'Unassigned' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Product</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->product->name }}</dd>
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
                    <dt class="text-sm font-semibold text-madani-muted">Quantity</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->quantity }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Expiry</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->expired_date?->format('M j, Y') ?? 'Never' }}</dd>
                    <dd class="mt-2">
                        @if ($license->isExpired())
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Expired</span>
                        @elseif ($daysUntilExpiry !== null && $daysUntilExpiry <= 30)
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">{{ $daysUntilExpiry }} days left</span>
                        @else
                            <span class="rounded-full bg-madani-pale px-3 py-1 text-xs font-semibold text-madani-green">{{ $license->expired_date ? 'Active' : 'No expiry' }}</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Activations</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $license->activeActivationCount() }} active / {{ $license->max_activations ?? 'unlimited' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-madani-muted">License key</dt>
                    <dd class="mt-2">
                        <x-license-key-display :license="$license" :reveal-url="route('admin.licenses.show-key', $license)" />
                    </dd>
                </div>
            </dl>
        </x-card>

        <div class="space-y-6">
            <x-card>
                <p class="text-sm font-semibold text-madani-muted">Activation support</p>
                <p class="mt-3 text-4xl font-extrabold text-madani-deep">{{ $license->activeActivationCount() }}</p>
                <p class="mt-2 text-sm leading-6 text-madani-muted">Use reset only when a customer changes devices or support needs to clear activation history.</p>

                <form method="POST" action="{{ route('admin.licenses.reset-activation', $license) }}" class="mt-6 space-y-4" onsubmit="return confirm('Reset activations for this license?')">
                    @csrf
                    <div>
                        <x-form-label for="strategy" value="Reset strategy" />
                        <select id="strategy" name="strategy" class="vd-input mt-2">
                            <option value="delete">Remove activation records</option>
                            <option value="deactivate">Mark activations inactive</option>
                        </select>
                    </div>
                    <x-button variant="secondary">Reset activations</x-button>
                </form>
            </x-card>

            <x-card>
                <p class="text-sm font-semibold text-madani-muted">Danger zone</p>
                <p class="mt-2 text-sm leading-6 text-madani-muted">Deleting a license removes its activation records through database cascade rules.</p>
                <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}" class="mt-6" onsubmit="return confirm('Delete this license?')">
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger">Delete license</x-button>
                </form>
            </x-card>
        </div>
    </div>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="border-b border-madani-border bg-madani-ghost px-6 py-5">
            <h2 class="text-lg font-bold text-madani-deep">Sub-product licenses</h2>
            <p class="mt-1 text-sm text-madani-muted">License keys issued for sub-products under this parent product.</p>
        </div>

        @php
            $subProductLicenses = \App\Models\License::query()
                ->where('product_id', $license->product_id)
                ->where('sub_product_id', '!=', null)
                ->where('user_id', $license->user_id)
                ->with('subProduct')
                ->orderBy('sub_product_id')
                ->get();
        @endphp

        @if ($subProductLicenses->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-madani-muted">No sub-product keys added yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-madani-border">
                    <thead class="bg-vd-secondary">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Sub-Product</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">License Key</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Activations</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-madani-border bg-vd-surface">
                        @foreach ($subProductLicenses as $subLicense)
                            <tr>
                                <td class="px-6 py-4 text-sm text-madani-deep">{{ $subLicense->subProduct?->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 font-mono text-sm text-madani-muted">{{ $subLicense->masked_license_key }}</td>
                                <td class="px-6 py-4 text-sm text-madani-muted">{{ $subLicense->activeActivationCount() }} / {{ $subLicense->max_activations ?? 'Unlimited' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($subLicense->isExpired())
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Expired</span>
                                    @else
                                        <span class="rounded-full bg-madani-pale px-3 py-1 text-xs font-semibold text-madani-green">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.licenses.destroy', $subLicense) }}" onsubmit="return confirm('Remove this sub-product license?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="ghost">Delete</x-button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <x-card class="mt-6 overflow-hidden p-0">
        <div class="border-b border-madani-border bg-madani-ghost px-6 py-5">
            <h2 class="text-lg font-bold text-madani-deep">Activation records</h2>
            <p class="mt-1 text-sm text-madani-muted">Week 3 public activation will populate these records through the installer API.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-madani-border">
                <thead class="bg-vd-secondary">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Device</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Hostname</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">IP address</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Activated</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-[0.14em] text-madani-muted">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-madani-border bg-vd-surface">
                    @forelse ($license->activations as $activation)
                        <tr>
                            <td class="px-6 py-4 font-mono text-sm text-madani-deep">{{ $activation->device_id }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $activation->hostname ?? 'No hostname' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $activation->ip_address ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $activation->location ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ ucfirst($activation->status) }}</td>
                            <td class="px-6 py-4 text-sm text-madani-muted">{{ $activation->created_at?->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.licenses.activation.destroy', $activation) }}" onsubmit="return confirm('Remove this activation record?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="ghost">Delete</x-button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-madani-muted">
                                No activations recorded yet. Support reset actions will appear here once activations exist.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div id="reveal-key-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-madani-depth/70 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-madani-deep">Plaintext license key</h2>
                    <p class="mt-2 text-sm leading-6 text-madani-muted">Use this only when customer support needs the original key.</p>
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
