@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <x-page-header title="Edit user" subtitle="{{ $user->name }}" />

    <x-card>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @method('PUT')
            @include('admin.users._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
