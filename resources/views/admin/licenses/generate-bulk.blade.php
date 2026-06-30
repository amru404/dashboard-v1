@extends('layouts.admin')

@section('title', 'Generate Bulk Licenses')

@section('content')
    <x-page-header title="New Multiple License" subtitle="Create and configure a new multiple license .">
        <x-slot name="actions">
        </x-slot>
    </x-page-header>


<form 
    method="POST" 
    action="{{ route('admin.licenses.bulk-store') }}" 
    class="space-y-6"
    x-data="bulkLicenseGenerator"
    @submit.prevent="handleSubmit"
>
   @include('admin.licenses._form-bulk')
</form>

@endsection
