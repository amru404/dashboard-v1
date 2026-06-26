@extends('layouts.admin')

@section('title', 'Edit Invoice')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Edit Invoice</h1>
    <p class="text-base text-gray-300">
        Update invoice details and user assignments.
    </p>
</div>

{{-- ── Form ── --}}
@include('admin.invoices._form', ['invoice' => $invoice, 'users' => $users])

@endsection
