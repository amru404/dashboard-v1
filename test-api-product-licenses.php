<?php

// Test product licenses API endpoint

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Product Licenses API ===\n\n";

// Find DIS product
$dis = \App\Models\Product::where('code', 'DIS')->first();

if (!$dis) {
    echo "❌ DIS product not found\n";
    exit(1);
}

echo "✅ Found DIS Product: {$dis->name} (ID: {$dis->id})\n\n";

// Check children
echo "📁 Children of DIS:\n";
foreach ($dis->children as $child) {
    echo "   - {$child->name} (ID: {$child->id})\n";
}

echo "\n";

// Check licenses for DIS (product_id=DIS.id, sub_product_id=null)
$disLicenses = \App\Models\License::where('product_id', $dis->id)
    ->whereNull('sub_product_id')
    ->get();

echo "📄 Licenses for DIS (parent product):\n";
echo "   Count: {$disLicenses->count()}\n";
foreach ($disLicenses as $lic) {
    echo "   - {$lic->license_key} (product_id={$lic->product_id}, sub_product_id={$lic->sub_product_id})\n";
}

echo "\n";

// Check licenses for Report Builder (sub_product_id=RB.id)
$reportBuilder = \App\Models\Product::where('name', 'like', '%Report Builder%')
    ->orWhere('name', 'like', '%report%')
    ->first();

if ($reportBuilder) {
    echo "📄 Found Report Builder: {$reportBuilder->name} (ID: {$reportBuilder->id})\n";
    
    $rbLicenses = \App\Models\License::where('sub_product_id', $reportBuilder->id)->get();
    echo "   Licenses count: {$rbLicenses->count()}\n";
    
    foreach ($rbLicenses as $lic) {
        echo "   - {$lic->license_key} (product_id={$lic->product_id}, sub_product_id={$lic->sub_product_id})\n";
    }
} else {
    echo "❌ Report Builder product not found\n";
}

echo "\n";

// Test API endpoint
echo "🚀 Testing API: POST /api/product/licenses\n";
echo "   Body: {\"product_code\": \"DIS\"}\n\n";

// Make request
$ch = curl_init('http://localhost:8000/api/product/licenses');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['product_code' => 'DIS']));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: {$httpCode}\n\n";

$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo "\n";

// Check structure
echo "\n📊 API Response Structure:\n";
echo "   Product: {$data['product_name']}\n";
echo "   Parent Licenses: " . count($data['licenses']) . "\n";
if (!empty($data['children'])) {
    echo "   Children Count: " . count($data['children']) . "\n";
    foreach ($data['children'] as $child) {
        echo "     - {$child['product_name']}: " . count($child['licenses']) . " licenses\n";
    }
}
