@extends('layouts.admin')

@section('title', 'New License')

@section('content')
    <x-page-header title="New license" subtitle="Create an encrypted installer-facing license for a customer user.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.batch-create')">Batch issue</x-button>
        </x-slot>
    </x-page-header>

    <x-card>
        <form method="POST" action="{{ route('admin.licenses.store') }}" data-batch-url="{{ route('admin.licenses.batch-store') }}" data-token="{{ csrf_token() }}">
            @include('admin.licenses._form', ['submitLabel' => 'Create license'])
        </form>
    </x-card>
@endsection
