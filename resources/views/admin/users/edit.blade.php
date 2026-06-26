@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    {{-- ── Page Header ── --}}
    <div class="mb-8">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">Edit user</h1>
                <p class="text-base text-gray-300">
                    {{ $user->name }}
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2.5 rounded-lg text-gray-300 hover:text-white font-semibold text-sm transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </div>

    {{-- ── Form ── --}}
    <div class="vd-card border-[#2a3f5f]">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @method('PUT')
            @include('admin.users._form', ['submitLabel' => 'Save changes'])
        </form>
    </div>
@endsection
