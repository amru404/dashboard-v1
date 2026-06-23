@php
    $isAdmin = Auth::user()->role === 'admin';
    $layout  = $isAdmin ? 'layouts.admin' : 'layouts.user';
@endphp
@extends($layout)

@section('title', 'Profile')

@section('content')
    <x-page-header title="Profile Settings" subtitle="Manage your account information and security." />

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Update profile info --}}
        <div class="vd-card lg:col-span-2">
            <h2 class="text-label-lg text-vd-on-surface mb-5">Profile Information</h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Password --}}
        <div class="vd-card">
            <h2 class="text-label-lg text-vd-on-surface mb-5">Update Password</h2>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete account --}}
        <div class="vd-card border-vd-error/25 lg:col-span-3">
            <h2 class="text-label-lg text-vd-error mb-5">Danger Zone</h2>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
