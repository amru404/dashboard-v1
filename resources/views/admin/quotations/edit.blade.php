@extends('layouts.admin')

@section('title', 'Edit Quotation')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Edit Quotation</h1>
    <p class="text-base text-gray-300">
        Update quotation details and user assignments.
    </p>
</div>

{{-- ── Form ── --}}
@include('admin.quotations._form', ['quotation' => $quotation, 'users' => $users])

@endsection
