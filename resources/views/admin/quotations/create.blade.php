@extends('layouts.admin')

@section('title', 'Create Quotation')

@section('content')

{{-- ── Page Header ── --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-2">Create Quotation</h1>
    <p class="text-base text-gray-300">
        Create a new quotation and assign it to users.
    </p>
</div>

{{-- ── Form ── --}}
@include('admin.quotations._form', ['quotation' => null, 'users' => $users])

@endsection
