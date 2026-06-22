@extends('layouts.admin')

@section('title', 'Issue License')

@section('content')
    <x-page-header title="Issue license" subtitle="Create an encrypted installer-facing license for a customer user.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.batch-create')">Batch issue</x-button>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.licenses.store') }}">
            @include('admin.licenses._form', ['submitLabel' => 'Create license'])
        </form>
    </x-card>
@endsection
