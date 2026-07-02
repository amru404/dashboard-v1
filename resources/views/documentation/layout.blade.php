@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.user')

@section('title', $title ?? 'Documentation')

@section('content')
<div class="mx-auto max-w-7xl">
    <x-page-header :title="$title ?? 'Documentation'" :subtitle="$subtitle ?? ''">
        <x-slot name="actions">
            <div class="flex gap-2">
                <x-button 
                    :href="route('documentation.user-guide')" 
                    :variant="request()->routeIs('documentation.user-guide') ? 'primary' : 'secondary'"
                    size="sm">
                    User Guide
                </x-button>
                @if(auth()->user()->isAdmin())
                <x-button 
                    :href="route('documentation.admin-guide')" 
                    :variant="request()->routeIs('documentation.admin-guide') ? 'primary' : 'secondary'"
                    size="sm">
                    Admin Guide
                </x-button>
                @endif
                <x-button 
                    :href="route('documentation.api')" 
                    :variant="request()->routeIs('documentation.api') ? 'primary' : 'secondary'"
                    size="sm">
                    API Documentation
                </x-button>
            </div>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">
        @yield('documentation-content')
    </div>
</div>
@endsection
