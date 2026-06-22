@extends('layouts.admin')

@section('title', 'Create License Type')

@section('content')
    <x-page-header title="Create license type" subtitle="Add a reusable license category for future license records." />

    <x-card>
        <form method="POST" action="{{ route('admin.license-types.store') }}">
            @include('admin.license-types._form', ['submitLabel' => 'Create license type'])
        </form>
    </x-card>
@endsection
