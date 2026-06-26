@extends('layouts.admin')

@section('title', 'Create Invoice')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Create Invoice</h1>
    <p class="text-base text-gray-300">
        Create a new invoice and assign it to users.
    </p>
</div>

{{-- ── Form ── --}}
@include('admin.invoices._form', ['invoice' => null, 'users' => $users])

@endsection
