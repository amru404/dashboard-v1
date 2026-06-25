@props(['license'])

<div x-data="{
        shown: false,
        loading: false,
        revealed: null,
        async toggle() {
            if (this.shown) {
                this.shown = false;
                return;
            }

            if (this.revealed) {
                this.shown = true;
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('{{ route('user.licenses.show-key', $license) }}', {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await response.json();

                if (! response.ok) {
                    throw new Error(data.message || 'Unable to reveal license key.');
                }

                this.revealed = data.license_key;
                this.shown = true;
            } catch (error) {
                window.console.error(error);
                this.revealed = null;
                alert(error.message || 'Unable to reveal license key.');
            } finally {
                this.loading = false;
            }
        },
    }"
    class="rounded-2xl border border-vd-border bg-[#0c1a2e] p-4 shadow-sm"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-white truncate">{{ $license->licenseType->name }}</p>
            <p class="text-sm text-vd-muted truncate">
                Expires: {{ $license->expired_date?->format('M j, Y') ?? 'Never' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs text-vd-muted">
            <span class="inline-flex items-center rounded-full border border-vd-border bg-vd-secondary/80 px-2.5 py-1">
                {{ $license->active_activations_count ?? $license->activeActivationCount() }} / {{ $license->max_activations ?? '∞' }} activations
            </span>
            <a href="{{ route('user.licenses.show', $license) }}" class="inline-flex items-center rounded-full border border-vd-border bg-white/5 px-3 py-2 text-xs font-semibold text-vd-primary transition hover:bg-white/10">
                View details
            </a>
        </div>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto] items-center">
        <div class="font-mono text-sm bg-black/20 rounded-2xl border border-vd-border px-3 py-3 text-vd-on-surface break-all">
            <span x-show="!shown" class="text-vd-muted">{{ $license->masked_license_key }}</span>
            <span x-show="shown" x-text="revealed ?? 'Loading…'" class="text-white"></span>
        </div>

        <button
            type="button"
            @click="toggle()"
            class="inline-flex items-center justify-center rounded-full border border-vd-border bg-vd-secondary px-4 py-2 text-xs font-semibold text-vd-primary transition hover:bg-vd-primary/10 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="loading"
        >
            <span x-show="!shown && !loading">Show</span>
            <span x-show="shown && !loading">Hide</span>
            <span x-show="loading">Loading…</span>
        </button>
    </div>
</div>
