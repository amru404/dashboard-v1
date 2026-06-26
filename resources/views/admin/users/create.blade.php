@extends('layouts.admin')

@section('title', 'New User')

@section('content')
    {{-- ── Page Header ── --}}
    <div class="mb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">New user</h1>
            <p class="text-base text-gray-300">
                Add an admin or customer account.
            </p>
        </div>
    </div>

    {{-- ── Form ── --}}
    <div class="vd-card border-[#2a3f5f]">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @include('admin.users._form', ['submitLabel' => 'New user'])
        </form>
    </div>
@endsection
