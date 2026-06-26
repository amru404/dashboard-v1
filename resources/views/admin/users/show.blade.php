@extends('layouts.admin')

@section('title', $user->name)

@section('content')
    {{-- ── Page Header ── --}}
    <div class="mb-8">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $user->name }}</h1>
                <p class="text-base text-gray-300">
                    User profile, role, and account status.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-gray-300 hover:text-white font-semibold text-sm transition-colors">
                    Edit user
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        {{-- ── Main Info Card ── --}}
        <div class="vd-card border-[#2a3f5f]">
            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Email</dt>
                    <dd class="mt-1 text-base font-semibold text-white">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Status</dt>
                    <dd class="mt-1">
                        @if ($user->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Active</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-500/20 text-gray-400 border border-gray-500/30">Inactive</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Role</dt>
                    <dd class="mt-1 text-base font-semibold capitalize text-white">{{ $user->role }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-400">Email verified</dt>
                    <dd class="mt-1 text-base text-gray-300">{{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y') : 'No' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-semibold text-gray-400">Organization</dt>
                    <dd class="mt-1 text-base text-gray-300">{{ $user->organization?->name ?? 'Unassigned' }}</dd>
                </div>
            </dl>
        </div>

        {{-- ── Account Controls Card ── --}}
        <div class="vd-card border-[#2a3f5f]">
            <p class="text-sm font-semibold text-white">Account controls</p>
            <p class="mt-3 text-sm leading-6 text-gray-400">
                Deleting a user removes the account from Customer Area. Your current admin account is protected from self-deletion.
            </p>

            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-6" onsubmit="return confirm('Delete this user?')">
                @csrf
                @method('DELETE')
                <button type="submit" @disabled($user->is(auth()->user())) class="inline-flex items-center px-4 py-2.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 font-semibold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Delete user
                </button>
            </form>
        </div>
    </div>
@endsection
