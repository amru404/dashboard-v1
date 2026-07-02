@extends('documentation.layout', ['title' => 'API Documentation', 'subtitle' => 'Complete API reference for license verification and management'])

@section('documentation-content')

{{-- API Overview --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
        </svg>
        API Overview
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">The Vericotech License Management API provides endpoints for license verification, activation, and product information retrieval. All endpoints use POST requests with JSON payloads.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Base URL:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4">
            <code class="text-vd-primary">http://user.vericotech.com/api</code>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Authentication:</h3>
        <p class="text-gray-300 mb-4">Currently, the API does not require authentication tokens. Rate limiting is applied: <strong>30 requests per minute</strong> per IP address.</p>

        <h3 class="text-lg font-semibold text-white mb-2">Content Type:</h3>
        <p class="text-gray-300 mb-4">All requests must include the header:</p>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4">
            <code class="text-gray-300">Content-Type: application/json</code>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Available Endpoints:</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-300">
            <li><code class="text-vd-primary">POST /api/license/verify</code> - Verify a license key</li>
            <li><code class="text-vd-primary">POST /api/license/activate</code> - Activate a license on a device</li>
            <li><code class="text-vd-primary">POST /api/user/products</code> - Get all products and licenses for a user</li>
            <li><code class="text-vd-primary">POST /api/product/licenses</code> - Get all licenses for a specific product</li>
        </ul>
    </div>
</div>


{{-- Endpoint 1: Verify License --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        1. Verify License
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Verify a license key and retrieve its details without activating it on a device.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Endpoint:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4">
            <code class="text-vd-primary">POST /api/license/verify</code>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Request Body:</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 mb-4">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Field</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Required</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">license_key</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string</td>
                        <td class="px-4 py-3 text-sm text-green-400">Yes</td>
                        <td class="px-4 py-3 text-sm text-gray-300">The license key to verify</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">device_fingerprint</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string</td>
                        <td class="px-4 py-3 text-sm text-yellow-400">No</td>
                        <td class="px-4 py-3 text-sm text-gray-300">Unique device identifier to check activation status</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Example Request:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "license_key": "ABC123-DEF456-GHI789",
  "device_fingerprint": "unique-device-id-12345"
}</code></pre>
        </div>


        <h3 class="text-lg font-semibold text-white mb-2">Response 200 (Success - Valid License):</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "valid": true,
  "product_name": "Digital Information System",
  "product_code": "DIS",
  "sub_product_name": "Report Builder",
  "sub_product_code": "DIS-REPORTS",
  "product_path": ["Digital Information System", "Report Builder"],
  "license_key": "ABC123-DEF456-GHI789",
  "license_type": "Enterprise",
  "organization_name": "Acme Corporation",
  "quantity": 1,
  "max_activations": 5,
  "expired_date": "2027-12-31",
  "is_expired": false,
  "is_activated": true,
  "is_activated_on_this_device": true,
  "active_count": 2,
  "can_activate": true,
  "activation": {
    "hostname": "DESKTOP-ABC123",
    "location": null,
    "ip_address": "192.168.1.100",
    "activated_at": "2026-01-15T10:30:00+00:00"
  }
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Error Responses:</h3>
        <div class="space-y-3">
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 404 - License Not Found</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "valid": false,
  "error": "License not found"
}</code></pre>
            </div>
        </div>
    </div>
</div>


{{-- Endpoint 2: Activate License --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        2. Activate License
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Activate a license key on a specific device. If the device is already activated, returns the existing activation details.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Endpoint:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4">
            <code class="text-vd-primary">POST /api/license/activate</code>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Request Body:</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 mb-4">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Field</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Required</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">license_key</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string</td>
                        <td class="px-4 py-3 text-sm text-green-400">Yes</td>
                        <td class="px-4 py-3 text-sm text-gray-300">The license key to activate</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">device_fingerprint</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string</td>
                        <td class="px-4 py-3 text-sm text-green-400">Yes</td>
                        <td class="px-4 py-3 text-sm text-gray-300">Unique device identifier for activation</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">hostname</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string</td>
                        <td class="px-4 py-3 text-sm text-yellow-400">No</td>
                        <td class="px-4 py-3 text-sm text-gray-300">Device hostname (max 255 characters)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Example Request:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "license_key": "ABC123-DEF456-GHI789",
  "device_fingerprint": "unique-device-id-12345",
  "hostname": "DESKTOP-ABC123"
}</code></pre>
        </div>


        <h3 class="text-lg font-semibold text-white mb-2">Response 201 (Success - New Activation):</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "valid": true,
  "activated": true,
  "product_name": "Digital Information System",
  "product_code": "DIS",
  "sub_product_name": "Report Builder",
  "sub_product_code": "DIS-REPORTS",
  "product_path": ["Digital Information System", "Report Builder"],
  "license_key": "ABC123-DEF456-GHI789",
  "organization_name": "Acme Corporation",
  "activation": {
    "hostname": "DESKTOP-ABC123",
    "location": null,
    "ip_address": "192.168.1.100",
    "activated_at": "2026-07-02T14:30:00+00:00"
  }
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Response 200 (Success - Already Activated):</h3>
        <p class="text-gray-300 mb-2">If the device is already activated, returns existing activation information.</p>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "valid": true,
  "activated": true,
  "product_name": "Digital Information System",
  "product_code": "DIS",
  "sub_product_name": "Report Builder",
  "sub_product_code": "DIS-REPORTS",
  "product_path": ["Digital Information System", "Report Builder"],
  "license_key": "ABC123-DEF456-GHI789",
  "organization_name": "Acme Corporation",
  "activation": {
    "hostname": "DESKTOP-ABC123",
    "location": null,
    "ip_address": "192.168.1.100",
    "activated_at": "2026-01-15T10:30:00+00:00"
  }
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Error Responses:</h3>
        <div class="space-y-3">
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 404 - License Not Found</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "valid": false,
  "error": "License not found"
}</code></pre>
            </div>
            
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 403 - License Expired</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "valid": false,
  "error": "License has expired",
  "expired_date": "2025-12-31"
}</code></pre>
            </div>
            
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 403 - Activation Limit Reached</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "valid": false,
  "error": "Activation limit reached",
  "max_activations": 5,
  "active_count": 5
}</code></pre>
            </div>
        </div>
    </div>
</div>


{{-- Endpoint 3: User Products --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        3. Get User Products
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Retrieve all products and licenses for a specific user by email address. Returns a hierarchical tree structure of products with their licenses.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Endpoint:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4">
            <code class="text-vd-primary">POST /api/user/products</code>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Request Body:</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 mb-4">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Field</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Required</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">email</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string (email)</td>
                        <td class="px-4 py-3 text-sm text-green-400">Yes</td>
                        <td class="px-4 py-3 text-sm text-gray-300">User's email address</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Example Request:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "email": "user@example.com"
}</code></pre>
        </div>


        <h3 class="text-lg font-semibold text-white mb-2">Response 200 (Success):</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "user": {
    "name": "John Doe",
    "email": "user@example.com",
    "organization": "Acme Corporation"
  },
  "products": [
    {
      "product_name": "Digital Information System",
      "product_code": "DIS",
      "is_parent": true,
      "entitlement": {
        "status": "active",
        "start_date": "2026-01-01",
        "end_date": null,
        "download_expired_date": "2027-12-31"
      },
      "license": {
        "license_key": "ABC123-DEF456-GHI789",
        "is_parent_only": true,
        "license_type": "Enterprise",
        "quantity": 1,
        "max_activations": 5,
        "expired_date": "2027-12-31",
        "is_expired": false,
        "active_count": 2
      },
      "children": [
        {
          "product_name": "Report Builder",
          "product_code": "DIS-REPORTS",
          "is_sub_product": true,
          "parent_product_code": "DIS",
          "licenses": [
            {
              "license_key": "XYZ789-ABC123-DEF456",
              "license_type": "Standard",
              "quantity": 1,
              "max_activations": 3,
              "expired_date": "2027-06-30",
              "is_expired": false,
              "active_count": 1
            }
          ],
          "children": []
        }
      ]
    }
  ]
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Error Responses:</h3>
        <div class="space-y-3">
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 404 - User Not Found</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "success": false,
  "error": "User not found"
}</code></pre>
            </div>
        </div>
    </div>
</div>


{{-- Endpoint 4: Product Licenses --}}
<div class="rounded-lg border border-vd-border bg-vd-card p-6">
    <h2 class="mb-4 text-2xl font-bold text-white flex items-center gap-2">
        <svg class="w-6 h-6 text-vd-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        4. Get Product Licenses
    </h2>
    <div class="prose prose-invert max-w-none">
        <p class="text-gray-300 mb-4">Retrieve all licenses for a specific product by product code. Optionally filter by user email. Returns a hierarchical structure including sub-products.</p>
        
        <h3 class="text-lg font-semibold text-white mb-2">Endpoint:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4">
            <code class="text-vd-primary">POST /api/product/licenses</code>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Request Body:</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 mb-4">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Field</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Required</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">product_code</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string</td>
                        <td class="px-4 py-3 text-sm text-green-400">Yes</td>
                        <td class="px-4 py-3 text-sm text-gray-300">Product code to get licenses for</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-mono text-vd-primary">email</td>
                        <td class="px-4 py-3 text-sm text-gray-300">string (email)</td>
                        <td class="px-4 py-3 text-sm text-yellow-400">No</td>
                        <td class="px-4 py-3 text-sm text-gray-300">Filter licenses by user email</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Example Request:</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "product_code": "DIS",
  "email": "user@example.com"
}</code></pre>
        </div>


        <h3 class="text-lg font-semibold text-white mb-2">Response 200 (Success):</h3>
        <div class="p-4 bg-gray-900 border border-gray-700 rounded-lg mb-4 overflow-x-auto">
            <pre class="text-sm text-gray-300"><code>{
  "product_name": "Digital Information System",
  "product_code": "DIS",
  "total_licenses": 9,
  "licenses": [
    {
      "license_key": "ABC123-DEF456-GHI789",
      "is_parent_only": true,
      "license_type": "Enterprise",
      "user_name": "John Doe",
      "user_email": "user@example.com",
      "organization": "Acme Corporation",
      "quantity": 1,
      "max_activations": 5,
      "expired_date": "2027-12-31",
      "is_expired": false,
      "active_count": 2
    }
  ],
  "children": [
    {
      "product_name": "Connector",
      "product_code": "DIS-CONNECTOR",
      "is_sub_product": true,
      "parent_product_code": "DIS",
      "licenses": [
        {
          "license_key": "CON123-ABC456-XYZ789",
          "is_parent_only": false,
          "license_type": "Standard",
          "user_name": "John Doe",
          "user_email": "user@example.com",
          "organization": "Acme Corporation",
          "quantity": 1,
          "max_activations": 3,
          "expired_date": "2027-06-30",
          "is_expired": false,
          "active_count": 1
        }
      ],
      "children": []
    },
    {
      "product_name": "Report Builder",
      "product_code": "DIS-REPORTS",
      "is_sub_product": true,
      "parent_product_code": "DIS",
      "licenses": [
        {
          "license_key": "REP123-XYZ789-ABC456",
          "is_parent_only": false,
          "license_type": "Professional",
          "user_name": "Jane Smith",
          "user_email": "jane@example.com",
          "organization": "Tech Solutions",
          "quantity": 1,
          "max_activations": null,
          "expired_date": null,
          "is_expired": false,
          "active_count": 5
        }
      ],
      "children": []
    }
  ]
}</code></pre>
        </div>

        <h3 class="text-lg font-semibold text-white mb-2">Error Responses:</h3>
        <div class="space-y-3">
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 404 - Product Not Found</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "success": false,
  "error": "Product not found"
}</code></pre>
            </div>
            
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                <p class="text-red-300 text-sm font-semibold mb-1">Status 404 - User Not Found (when email provided)</p>
                <pre class="text-sm text-gray-300 mt-2"><code>{
  "success": false,
  "error": "User not found"
}</code></pre>
            </div>
        </div>
    </div>
</div>

@endsection
