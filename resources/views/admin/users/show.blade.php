@extends('layouts.admin')

@section('title', $user->name)

@section('content')
    <x-page-header title="{{ $user->name }}" subtitle="User profile, role, and account status.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.users.edit', $user)">Edit user</x-button>
        </x-slot>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <x-card>
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Email</dt>
                    <dd class="mt-1 text-base font-semibold text-madani-deep">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Status</dt>
                    <dd class="mt-1"><x-badge :active="$user->is_active" /></dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Role</dt>
                    <dd class="mt-1 text-base font-semibold capitalize text-madani-deep">{{ $user->role }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-madani-muted">Email verified</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y') : 'No' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-madani-muted">Organization</dt>
                    <dd class="mt-1 text-base text-madani-deep">{{ $user->organization?->name ?? 'Unassigned' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <p class="text-sm font-semibold text-madani-muted">Account controls</p>
            <p class="mt-3 text-sm leading-6 text-madani-muted">
                Deleting a user removes the account from Customer Area. Your current admin account is protected from self-deletion.
            </p>

            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-6" onsubmit="return confirm('Delete this user?')">
                @csrf
                @method('DELETE')
                <x-button variant="danger" :disabled="$user->is(auth()->user())">Delete user</x-button>
            </form>
        </x-card>
    </div>
@endsection
