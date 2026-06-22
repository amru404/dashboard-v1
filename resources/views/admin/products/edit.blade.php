@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <x-page-header title="Edit product" subtitle="{{ $product->name }}" />

    <x-card>
        <form method="POST" action="{{ route('admin.products.update', $product) }}">
            @method('PUT')
            @include('admin.products._form', ['submitLabel' => 'Save changes'])
        </form>
    </x-card>
@endsection
