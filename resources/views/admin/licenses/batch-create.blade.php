@extends('layouts.admin')

@section('title', 'Batch Issue Licenses')

@section('content')
    <x-page-header title="Batch issue licenses" subtitle="Generate multiple encrypted license records with the same customer, product, and license type configuration.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.create')">Issue single license</x-button>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.licenses.batch-store') }}">
            @csrf

            <div class="grid gap-5 lg:grid-cols-2">
                <div>
                    <x-form-label for="user_id" value="Customer user" />
                    <select id="user_id" name="user_id" class="madani-input mt-2" required>
                        <option value="">Select customer</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>
                                {{ $user->name }} - {{ $user->email }} - {{ $user->organization?->name ?? 'Unassigned' }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="license_type_id" value="License type" />
                    <select id="license_type_id" name="license_type_id" class="madani-input mt-2" required>
                        <option value="">Select type</option>
                        @foreach ($licenseTypes as $licenseType)
                            <option value="{{ $licenseType->id }}" @selected((string) old('license_type_id') === (string) $licenseType->id)>
                                {{ $licenseType->name }} ({{ $licenseType->code }}){{ $licenseType->is_active ? '' : ' - inactive' }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('license_type_id')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="product_id" value="Product" />
                    <select id="product_id" name="product_id" class="madani-input mt-2" required>
                        <option value="">Select product</option>
                        @foreach ($productOptions as $option)
                            <option value="{{ $option['id'] }}" @selected((string) old('product_id') === (string) $option['id'])>
                                {{ $option['label'] }} - {{ $option['path'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-madani-muted">Indented by recursive product hierarchy.</p>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="sub_product_id" value="Sub-product" />
                    <select id="sub_product_id" name="sub_product_id" class="madani-input mt-2">
                        <option value="">No sub-product</option>
                        @foreach ($productOptions as $option)
                            <option value="{{ $option['id'] }}" @selected((string) old('sub_product_id') === (string) $option['id'])>
                                {{ $option['label'] }} - {{ $option['path'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-madani-muted">Uses the same products table. Choose a child or descendant of the main product.</p>
                    <x-input-error :messages="$errors->get('sub_product_id')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="quantity" value="Seats per license" />
                    <x-form-input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required class="mt-2" />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="license_count" value="Number of license records" />
                    <x-form-input id="license_count" name="license_count" type="number" min="1" max="100" value="{{ old('license_count', 5) }}" required class="mt-2" />
                    <p class="mt-2 text-xs text-madani-muted">Each record receives its own generated encrypted key.</p>
                    <x-input-error :messages="$errors->get('license_count')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="max_activations" value="Max activations per license" />
                    <x-form-input id="max_activations" name="max_activations" type="number" min="1" value="{{ old('max_activations') }}" placeholder="Unlimited when blank" class="mt-2" />
                    <x-input-error :messages="$errors->get('max_activations')" class="mt-2" />
                </div>

                <div>
                    <x-form-label for="expired_date" value="Expiry date" />
                    <x-form-input id="expired_date" name="expired_date" type="date" value="{{ old('expired_date') }}" class="mt-2" />
                    <x-input-error :messages="$errors->get('expired_date')" class="mt-2" />
                </div>

                <div class="rounded-xl border border-madani-border bg-madani-ghost p-4 lg:col-span-2">
                    <x-form-label for="sample_license_key" value="Generated key sample" />
                    <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                        <input id="sample_license_key" type="text" class="madani-input font-mono uppercase" readonly placeholder="Generate a sample key">
                        <button
                            id="generate-sample-license-key"
                            type="button"
                            data-url="{{ route('admin.licenses.generate-key') }}"
                            data-token="{{ csrf_token() }}"
                            class="inline-flex items-center justify-center rounded-lg border border-madani-deep px-4 py-2 text-sm font-semibold text-madani-deep transition hover:bg-madani-deep hover:text-white"
                        >
                            Generate Key
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-madani-muted">This sample is not submitted. Batch creation generates one unique key per saved license.</p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <x-button>Create batch</x-button>
                <x-button variant="secondary" :href="route('admin.licenses.index')">Cancel</x-button>
            </div>
        </form>
    </x-card>

    <script>
        (() => {
            const input = document.getElementById('sample_license_key');
            const button = document.getElementById('generate-sample-license-key');

            button?.addEventListener('click', async () => {
                const response = await fetch(button.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': button.dataset.token,
                    },
                });
                const data = await response.json();

                input.value = data.license_key || '';
            });
        })();
    </script>
@endsection
