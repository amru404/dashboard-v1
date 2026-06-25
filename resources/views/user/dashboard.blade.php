@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

{{-- ── Hero Welcome Card ── --}}
<div class="vd-card mb-8 !p-8 md:!p-12 border-[#2a3f5f]">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="flex-1">
            <p class="text-eyebrow tracking-[0.18em] text-vd-primary uppercase mb-3">SOFTWARE ACCESS</p>
            @auth
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Welcome back, {{ auth()->user()->name }}</h1>
            @endauth
            <p class="text-base text-gray-300 leading-relaxed mb-6 max-w-2xl">
                Manage software access across teams — without the complexity.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('user.licenses.index') }}">
                    <x-primary-button>
                        View Licenses
                    </x-primary-button>
                </a>
                <a href="{{ route('user.downloads.index') }}">
                    <x-secondary-button>
                        Downloads
                    </x-secondary-button>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Stats Row ── --}}
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    @php
        $stats = [
            ['label' => 'Active Licenses',   'value' => $activeLicenseCount,'sublabel' => 'across ' . $activeEntitlementCount . ' products',  'color' => 'text-vd-success', 'icon' => 'far fa-check-circle'],
            ['label' => 'Downloads Available', 'value' => $downloadCount, 'sublabel' => 'files accessible', 'color' => 'text-vd-primary', 'icon' => 'fa-regular fa-circle-down'],
            ['label' => 'Device Activations', 'value' => $activeLicenseCount> 0 ? $activeLicenseCount . ' / ' . $licenseCount : '0 / 0', 'sublabel' => 'slots available', 'color' => 'text-vd-success', 'icon' => 'fa-solid fa-power-off'],
            ['label' => 'License Expired Soon', 'value' => $expiringLicenseCount, 'sublabel' => 'expired licenses ' . $expiredLicenseCount , 'color' => 'text-vd-warning', 'icon' => 'fas fa-triangle-exclamation'],
        ];
    @endphp

    @foreach ($stats as $stat)
    <div class="vd-card border-[#2a3f5f]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 font-semibold">
                    {{ $stat['label'] }}
                </p>
                <p class="text-3xl font-bold mb-1">
                    {{ $stat['value'] }}
                </p>
                <p class="text-sm text-gray-400">
                    {{ $stat['sublabel'] }}
                </p>
            </div>

            <div class="ml-4 {{ $stat['color'] }}">
               <i class="text-xl {{ $stat['icon'] }}"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid gap-4 lg:grid-cols-3 mb-8">

    <div class="lg:col-span-2">
        <h2 class="text-2xl font-bold text-white mb-4">Your Products</h2>
        
        <div class="space-y-4"  >
            @forelse ($ownedProducts as $product)
                <div class="vd-card border-[#2a3f5f] !p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-1">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-400">
                                Organization: {{ Auth::user()->organization?->name ?? '-' }}
                            </p>
                        </div>
                        <x-badge :active="$product->is_active">{{ $product->is_active ? 'Active' : 'Inactive' }}</x-badge>
                    </div>
                    
                    @php
                        $subProducts = $product->subProducts ?? collect();
                        $totalSubProducts = $subProducts->count();
                        $displaySubProducts = $subProducts->take(4);
                        $remainingCount = $totalSubProducts - 4;
                    @endphp
                    
                    @if ($totalSubProducts > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach ($displaySubProducts as $subProduct)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-[#0f1829] text-gray-300 border border-[#2a3f5f]">
                                    {{ $subProduct->name }}
                                </span>
                            @endforeach
                            
                            @if ($remainingCount > 0)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-[#0f1829] text-gray-400 border border-[#2a3f5f]">
                                    +{{ $remainingCount }} more...
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <a href="{{ route('user.products.show', $product) }}" class="text-sm font-semibold text-vd-primary hover:text-vd-primary/80 transition-colors">
                        View Details →
                    </a>
                </div>
            @empty
                <div class="vd-card border-[#2a3f5f] !p-12 text-center">
                    <p class="text-gray-400">No products are currently assigned to your account.</p>
                </div>
            @endforelse
        </div>
        
        @if ($ownedProducts->count() > 5)
            <div class="mt-4 text-center">
                <a href="{{ route('user.products.index') }}">
                    <x-primary-button>
                        View All Products →
                    </x-primary-button>
                </a>
            </div>
        @endif
    </div>
    
    <div>
        <h2 class="text-2xl font-bold text-white mb-4">Recent Lisences Activity</h2>
    
        <div class="vd-card border-[#2a3f5f] !p-6 mb-6">
            <div class="space-y-5">
                @forelse ($recentLicenses as $lisence)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-vd-warning bg-vd-warning/10">
                                   <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                    <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                                    <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                    </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white">{{ $lisence->product->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $lisence->licenseType->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $lisence->expired_date?->format('M j, Y') ?? 'Never'  }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-400">No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
