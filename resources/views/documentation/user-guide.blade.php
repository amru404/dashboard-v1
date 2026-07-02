@extends('documentation.layout', ['title' => 'User Guide', 'subtitle' => 'Learn how to use the Vericotech License Management System'])

@section('documentation-content')

{{-- Dashboard --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">The Dashboard provides an overview of your licenses and quick access to important information.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Key Features:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>Active Licenses:</strong> View the total number of your active licenses</li>
            <li><strong>Products:</strong> See how many products you have access to</li>
            <li><strong>Activations:</strong> Monitor your device activations</li>
            <li><strong>Expiring Soon:</strong> Get alerts for licenses expiring within 30 days</li>
            <li><strong>Recent Invoices:</strong> Quick access to your latest invoices</li>
        </ul>
    </div>
</div>

{{-- My Licenses --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
        </svg>
        My Licenses
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Manage and view all your software licenses in one place.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">How to Use:</h3>
        <ol class="list-decimal list-inside space-y-3 text-gray-300">
            <li><strong>Browse Licenses:</strong> Licenses are organized by product. Click on a product to expand and view its licenses.</li>
            <li><strong>View License Key:</strong> Click the eye icon next to a masked license key to reveal the full key.</li>
            <li><strong>Check Status:</strong> Each license displays its status (Active, Expired, or days remaining).</li>
            <li><strong>Monitor Activations:</strong> See how many devices are currently using each license.</li>
            <li><strong>View Details:</strong> Click "View" to see full license details including activation history.</li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">License Information:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>License Key:</strong> Your unique activation code</li>
            <li><strong>License Type:</strong> Type of license (Enterprise, Standard, Educational, etc.)</li>
            <li><strong>Expiry Date:</strong> When the license expires (if applicable)</li>
            <li><strong>Max Activations:</strong> Maximum number of devices allowed</li>
            <li><strong>Active Count:</strong> Currently activated devices</li>
        </ul>
    </div>
</div>

{{-- Downloads --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        Downloads
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Access software installers and documentation for your licensed products.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">How to Download:</h3>
        <ol class="list-decimal list-inside space-y-3 text-gray-300">
            <li><strong>Find Your Product:</strong> Use the search bar or browse by product category.</li>
            <li><strong>Filter Downloads:</strong> Filter by product to find specific files.</li>
            <li><strong>Click Download:</strong> Click the "Download" button to get the file.</li>
            <li><strong>Check Version:</strong> Make sure you're downloading the correct version for your license.</li>
        </ol>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Download Information:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><strong>File Name:</strong> Name and type of the file</li>
            <li><strong>Version:</strong> Software version number</li>
            <li><strong>File Size:</strong> Size of the download</li>
            <li><strong>Platform:</strong> Compatible operating system</li>
            <li><strong>Release Date:</strong> When this version was released</li>
        </ul>

        <div class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
            <p class="text-yellow-300 text-sm"><strong>Note:</strong> Some downloads may require an active, non-expired license to access.</p>
        </div>
    </div>
</div>

{{-- License Activation --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        License Activation
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Learn how to activate your software licenses on your devices.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Activation Methods:</h3>
        
        <h4 class="text-md font-semibold text-white mt-4 mb-2">Method 1: Within the Software</h4>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Open the installed software</li>
            <li>Navigate to Help → Activate License or License → Activate</li>
            <li>Enter your license key exactly as shown in "My Licenses"</li>
            <li>Click "Activate" and wait for confirmation</li>
        </ol>

        <h4 class="text-md font-semibold text-white mt-4 mb-2">Method 2: Using the API</h4>
        <p class="text-gray-300">For developers integrating license activation into their applications, see the <a href="{{ route('documentation.api') }}" class="text-vd-primary hover:underline">API Documentation</a>.</p>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Activation Limits:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li>Each license has a maximum number of allowed activations</li>
            <li>You can view your current activation count in "My Licenses"</li>
            <li>To activate on a new device when limit is reached, deactivate an old device first</li>
            <li>Some licenses have unlimited activations</li>
        </ul>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Troubleshooting:</h3>
        <div class="space-y-3">
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Error: "Activation limit reached"</p>
                <p class="text-gray-300 text-sm">Contact support to deactivate old devices or upgrade your license.</p>
            </div>
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Error: "License has expired"</p>
                <p class="text-gray-300 text-sm">Your license is no longer valid. Contact your administrator or support to renew.</p>
            </div>
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Error: "License not found"</p>
                <p class="text-gray-300 text-sm">Double-check your license key for typos. If the issue persists, contact support.</p>
            </div>
        </div>
    </div>
</div>

{{-- Need Help --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Need Help?
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">If you need additional assistance, our support team is here to help.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Contact Support:</h3>
        <div class="space-y-4">
            <div class="flex items-start gap-3 p-4 bg-vd-primary/10 border border-vd-primary/30 rounded-lg">
                <svg class="w-6 h-6 text-vd-primary flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <p class="font-semibold text-white mb-1">Email Support</p>
                    <a href="mailto:support@vericotech.com" class="text-vd-primary hover:underline">support@vericotech.com</a>
                    <p class="text-sm text-gray-400 mt-1">Response time: Within 24 hours</p>
                </div>
            </div>

            <div class="flex items-start gap-3 p-4 bg-vd-primary/10 border border-vd-primary/30 rounded-lg">
                <svg class="w-6 h-6 text-vd-primary flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
                <div>
                    <p class="font-semibold text-white mb-1">Visit Website</p>
                    <a href="https://vericotech.com" target="_blank" class="text-vd-primary hover:underline">www.vericotech.com</a>
                    <p class="text-sm text-gray-400 mt-1">Find more resources and documentation</p>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-semibold text-white mt-6 mb-2">Before Contacting Support:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li>Check your license status in "My Licenses"</li>
            <li>Verify you're using the correct license key</li>
            <li>Note any error messages you receive</li>
            <li>Prepare your license key and organization information</li>
        </ul>
    </div>
</div>

@endsection
