@extends('layouts.admin')

@section('title', 'Settings')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">System Settings</h1>
            <p class="text-base text-gray-300">
                Configure system-wide settings and preferences.
            </p>
        </div>
    </div>
</div>

{{-- Settings Form --}}
<div class="grid gap-6 lg:grid-cols-2">
    {{-- License Key Settings --}}
    <div class="vd-card border-[#2a3f5f] !p-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white mb-2">License Key Configuration</h2>
            <p class="text-sm text-gray-400">
                Set the default length for generated license keys. This will apply to all new keys created in the system.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="license_key_length" class="block text-sm font-semibold text-gray-300 mb-2">
                    License Key Length (characters)
                </label>
                <input 
                    type="number" 
                    id="license_key_length" 
                    name="license_key_length" 
                    value="{{ old('license_key_length', $licenseKeyLength) }}"
                    min="8"
                    max="256"
                    class="w-full px-4 py-2.5 bg-[#0f1829] border border-[#2a3f5f] rounded-lg text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-vd-primary focus:border-transparent"
                />
                <p class="text-xs text-gray-500 mt-1">
                    Minimum: 8 characters | Maximum: 256 characters
                </p>
                @error('license_key_length')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 mt-4">
                <p class="text-sm text-blue-300">
                    <span class="font-semibold">ℹ️ Current Setting:</span><br>
                    License keys will be generated with <strong>{{ $licenseKeyLength }}</strong> characters.
                </p>
            </div>

            <button 
                type="submit" 
                class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-vd-primary hover:bg-vd-primary/90 text-white font-semibold text-sm transition-colors"
            >
                Save Settings
            </button>
        </form>
    </div>

    {{-- Information Card --}}
    <div class="vd-card border-[#2a3f5f] !p-6">
        <h2 class="text-xl font-bold text-white mb-4">About License Keys</h2>
        
        <div class="space-y-4">
            <div class="border-l-2 border-vd-primary pl-4">
                <p class="text-sm font-semibold text-white mb-1">Format</p>
                <p class="text-sm text-gray-400">
                    License keys are generated in hexadecimal format (0-9, A-F) and formatted in 4-character segments separated by hyphens.
                </p>
            </div>

            <div class="border-l-2 border-vd-primary pl-4">
                <p class="text-sm font-semibold text-white mb-1">Security</p>
                <p class="text-sm text-gray-400">
                    Keys are encrypted in the database and only administrators can view the full key. Users see masked keys (last 4 characters only).
                </p>
            </div>

            <div class="border-l-2 border-vd-primary pl-4">
                <p class="text-sm font-semibold text-white mb-1">Example</p>
                <p class="text-sm text-gray-300 font-mono bg-[#0f1829] px-3 py-2 rounded mt-2">
                    4A7B-9C2E-F1D8-5B3A
                </p>
            </div>

            <div class="border-l-2 border-vd-primary pl-4">
                <p class="text-sm font-semibold text-white mb-1">When Changed</p>
                <p class="text-sm text-gray-400">
                    Only new license keys generated after this change will use the new length. Existing keys remain unchanged.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
