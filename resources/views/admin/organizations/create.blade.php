@extends('layouts.admin')

@section('title', 'New Organization')

@section('content')
    {{-- ── Page Header ── --}}
    <div class="mb-8">
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">New organization</h1>
            <p class="text-base text-gray-300">
                Add a new organization for account assignment.
            </p>
        </div>
    </div>

    {{-- ── Form ── --}}
    <div class="vd-card border-[#2a3f5f]">
        <form method="POST" action="{{ route('admin.organizations.store') }}">
            @include('admin.organizations._form', ['submitLabel' => 'New organization'])
        </form>
    </div>
@endsection
