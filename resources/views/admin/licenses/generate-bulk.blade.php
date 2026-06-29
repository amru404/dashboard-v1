@extends('layouts.admin')

@section('title', 'Generate Bulk Licenses')

@section('content')
    <x-page-header title="New license" subtitle="Create an encrypted installer-facing license for a customer user.">
        <x-slot name="actions">
            <x-button variant="secondary" :href="route('admin.licenses.batch-create')">Batch issue</x-button>
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
