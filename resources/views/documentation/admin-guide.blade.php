@extends('documentation.layout', ['title' => 'Admin Guide', 'subtitle' => 'Complete guide for administrators managing the license system'])

@section('documentation-content')

{{-- Admin Overview --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Admin Overview
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">The Admin Dashboard provides comprehensive statistics and quick actions for managing the entire license system.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Dashboard Statistics:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>Total Licenses:</strong> All licenses in the system</li>
            <li><strong>Active Licenses:</strong> Currently valid licenses</li>
            <li><strong>Total Users:</strong> Registered clients and partners</li>
            <li><strong>Total Products:</strong> Available software products</li>
            <li><strong>Recent Activities:</strong> Latest license creations and activations</li>
        </ul>
    </div>
</div>

{{-- Manage Products --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        Manage Products
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Products represent the software packages that can be licensed to customers.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Creating Products:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Navigate to <strong>Products</strong> in the admin menu</li>
            <li>Click <strong>New Product</strong></li>
            <li>Fill in product details (name, code, version, description)</li>
            <li>Select parent product if creating a sub-product</li>
            <li>Set product status (Active/Inactive)</li>
            <li>Click <strong>Save</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Product Hierarchy:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>Parent Products:</strong> Top-level products (e.g., "Enterprise Suite")</li>
            <li><strong>Sub-Products:</strong> Components of a parent (e.g., "Reporting Module")</li>
            <li>Products can have multiple levels of sub-products</li>
            <li>Licenses can be issued for either parent or sub-products</li>
        </ul>

        <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
            <p class="text-blue-300 text-sm"><strong>Tip:</strong> Use clear, unique product codes (e.g., "NAVARCH", "DIS-REPORTS") to avoid confusion when issuing licenses.</p>
        </div>
    </div>
</div>

{{-- Manage Licenses --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
        </svg>
        Manage Licenses
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Create, assign, and manage software licenses for customers.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Creating a Single License:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Go to <strong>Licenses</strong> → <strong>New License</strong></li>
            <li>Select the customer (user)</li>
            <li>Choose the product and sub-product (if applicable)</li>
            <li>Select license type</li>
            <li>Generate or enter a license key</li>
            <li>Set max activations (optional, leave blank for unlimited)</li>
            <li>Set expiry date (optional, leave blank for no expiry)</li>
            <li>Click <strong>Create License</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Bulk License Generation:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Go to <strong>Licenses</strong> page</li>
            <li>Find the product and click <strong>"Key"</strong> button</li>
            <li>Select sub-products to generate licenses for</li>
            <li>Set quantity, license type, and other options</li>
            <li>Click <strong>Generate Bulk Licenses</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Sharing Licenses:</h3>
        <p class="text-gray-300 mb-2">You can share existing licenses with other users without creating duplicates:</p>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Create a new license and select <strong>"Share License"</strong> mode</li>
            <li>Choose the source user who owns the license</li>
            <li>Select the product and specific license to share</li>
            <li>Choose the target user to share with</li>
            <li>Click <strong>Create License</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Managing Activations:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li>View all activations in license detail page</li>
            <li>Reset individual device activations</li>
            <li>Monitor activation history and device info</li>
            <li>Revoke access for shared licenses</li>
        </ul>

        <div class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
            <p class="text-yellow-300 text-sm"><strong>Important:</strong> License keys are encrypted. Use the "reveal" function only when absolutely necessary for customer support.</p>
        </div>
    </div>
</div>

{{-- Entitlements --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
        </svg>
        Entitlements
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Entitlements define which products customers have access to and for how long.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Creating Entitlements:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Navigate to <strong>Entitlements</strong></li>
            <li>Click <strong>New Entitlement</strong></li>
            <li>Select customer and product</li>
            <li>Set start date and end date (optional)</li>
            <li>Set download expiration date</li>
            <li>Set status (Active/Inactive)</li>
            <li>Click <strong>Create</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Entitlement Status:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>Active:</strong> Customer can access product downloads and use licenses</li>
            <li><strong>Inactive:</strong> Access is disabled regardless of dates</li>
            <li><strong>Expired:</strong> End date has passed</li>
        </ul>
    </div>
</div>

{{-- Manage Downloads --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        Manage Downloads
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Upload and manage software installers, updates, and documentation.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Adding Download Items:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Go to <strong>Downloads</strong></li>
            <li>Click <strong>New Download</strong></li>
            <li>Select associated product</li>
            <li>Enter title, version, and description</li>
            <li>Upload the file</li>
            <li>Set file type (Installer, Update, Documentation, etc.)</li>
            <li>Set platform (Windows, Mac, Linux, Web)</li>
            <li>Set status (Active/Inactive)</li>
            <li>Click <strong>Create</strong></li>
        </ol>

        <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
            <p class="text-blue-300 text-sm"><strong>Tip:</strong> Use clear version numbers and descriptions to help customers find the right download.</p>
        </div>
    </div>
</div>

{{-- Users & Organizations --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Users & Organizations
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Manage customer accounts and their organizations.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Creating Organizations:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Navigate to <strong>Organizations</strong></li>
            <li>Click <strong>New Organization</strong></li>
            <li>Enter organization details (name, address, contact)</li>
            <li>Click <strong>Create</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Creating Users:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Go to <strong>Users</strong></li>
            <li>Click <strong>New User</strong></li>
            <li>Enter user details (name, email, password)</li>
            <li>Select role (Client or Partner)</li>
            <li>Assign to an organization (optional)</li>
            <li>Click <strong>Create</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">User Roles:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>Admin:</strong> Full system access and management</li>
            <li><strong>Client:</strong> End customer with limited access to their licenses</li>
            <li><strong>Partner:</strong> Same access as Client (for resellers/partners)</li>
        </ul>
    </div>
</div>

{{-- Invoices & Quotations --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Manage Invoices & Quotations
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Create and manage invoices and quotations for customers.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Creating Invoices:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Navigate to <strong>Invoices</strong></li>
            <li>Click <strong>New Invoice</strong></li>
            <li>Select customer</li>
            <li>Enter invoice number and dates</li>
            <li>Add line items (description, quantity, price)</li>
            <li>Set status (Draft, Sent, Paid, Cancelled)</li>
            <li>Add notes if needed</li>
            <li>Click <strong>Create</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Creating Quotations:</h3>
        <p class="text-gray-300 mb-2">Similar process to invoices, used for price estimates before purchase.</p>
        
        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Invoice/Quotation Status:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>Draft:</strong> Still being prepared</li>
            <li><strong>Sent:</strong> Delivered to customer</li>
            <li><strong>Paid:</strong> Payment received (invoices only)</li>
            <li><strong>Cancelled:</strong> No longer valid</li>
        </ul>

        <div class="mt-4 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
            <p class="text-blue-300 text-sm"><strong>Tip:</strong> Customers can view and download their invoices and quotations from their dashboard.</p>
        </div>
    </div>
</div>

{{-- Settings --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Setting: License Key Length
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Configure system-wide settings for license key generation.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Configuring Key Length:</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Navigate to <strong>Settings</strong></li>
            <li>Find <strong>License Key Length</strong> setting</li>
            <li>Enter desired length (recommended: 16-32 characters)</li>
            <li>Click <strong>Save Settings</strong></li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Key Length Guidelines:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>8 characters:</strong> Minimum length key</li>
            <li><strong>16 characters:</strong> Basic security, easy to type</li>
            <li><strong>32 characters:</strong> High security, recommended for most cases</li>
            <li><strong>64 characters:</strong> Maximum security for sensitive applications</li>
        </ul>

        <div class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
            <p class="text-yellow-300 text-sm"><strong>Note:</strong> Changing key length only affects newly generated keys. Existing licenses are not modified.</p>
        </div>
    </div>
</div>

@endsection
