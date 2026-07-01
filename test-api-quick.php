<?php

// Quick API Test Script
// Run: php test-api-quick.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vericotech License API Test ===\n\n";

// Get a license for testing
$license = App\Models\License::with(['product', 'user', 'licenseType'])->first();

if (!$license) {
    echo "❌ No license found in database.\n";
    echo "Run: php artisan migrate:fresh --seed\n";
    exit(1);
}

echo "✅ Found test license:\n";
echo "   License Key: {$license->license_key}\n";
echo "   Product: {$license->product->name} ({$license->product->code})\n";
echo "   Owner: {$license->user->name} ({$license->user->email})\n";
echo "   Type: {$license->licenseType->name}\n";
echo "   Max Activations: " . ($license->max_activations ?? 'Unlimited') . "\n";
echo "   Expired Date: " . ($license->expired_date?->format('Y-m-d') ?? 'Never') . "\n\n";

// Test data
$testData = [
    'license_key' => $license->license_key,
    'user_email' => $license->user->email,
    'product_code' => $license->product->code,
    'device_fingerprint' => 'TEST-DEVICE-' . uniqid(),
];

echo "📝 Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

echo "🚀 API Endpoints Ready:\n";
echo "   POST /api/license/verify\n";
echo "   POST /api/license/activate\n";
echo "   POST /api/user/products\n";
echo "   POST /api/product/licenses\n\n";

echo "💡 Example cURL commands:\n\n";

echo "1. Verify License:\n";
echo "   curl -X POST http://localhost:8000/api/license/verify \\\n";
echo "     -H \"Content-Type: application/json\" \\\n";
echo "     -d '{\"license_key\": \"{$testData['license_key']}\"}'\n\n";

echo "2. Activate License:\n";
echo "   curl -X POST http://localhost:8000/api/license/activate \\\n";
echo "     -H \"Content-Type: application/json\" \\\n";
echo "     -d '{\"license_key\": \"{$testData['license_key']}\", \"device_fingerprint\": \"{$testData['device_fingerprint']}\", \"hostname\": \"MY-PC\"}'\n\n";

echo "3. Get User Products:\n";
echo "   curl -X POST http://localhost:8000/api/user/products \\\n";
echo "     -H \"Content-Type: application/json\" \\\n";
echo "     -d '{\"email\": \"{$testData['user_email']}\"}'\n\n";

echo "4. Get Product Licenses:\n";
echo "   curl -X POST http://localhost:8000/api/product/licenses \\\n";
echo "     -H \"Content-Type: application/json\" \\\n";
echo "     -d '{\"product_code\": \"{$testData['product_code']}\"}'\n\n";

echo "✅ API is ready for testing!\n";
