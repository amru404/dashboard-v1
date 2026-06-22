@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
    <x-page-header title="Create product" subtitle="Add a top-level product or nest it under an existing product." />

    <x-card>
        <form method="POST" action="{{ route('admin.products.store') }}">
            @include('admin.products._form', ['submitLabel' => 'Create product'])
        </form>
    </x-card>
@endsection
