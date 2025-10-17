<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RAILWAY DEPLOYMENT TEST ===\n";
echo "Testing if files are accessible...\n\n";

// Check if storage link exists
$storageLink = public_path('storage');
echo "Storage link path: $storageLink\n";
echo "Is link: " . (is_link($storageLink) ? 'YES' : 'NO') . "\n";
if (is_link($storageLink)) {
    echo "Points to: " . readlink($storageLink) . "\n";
}

// Check if image file exists
$imagePath = public_path('storage/apartment-covers/kynfrnmON9fPygUmAcH1akbS99655E9h13dREbxz.png');
echo "\nImage path: $imagePath\n";
echo "File exists: " . (file_exists($imagePath) ? 'YES' : 'NO') . "\n";

// Check directory contents
$dir = public_path('storage/apartment-covers/');
echo "\nDirectory: $dir\n";
echo "Directory exists: " . (is_dir($dir) ? 'YES' : 'NO') . "\n";
if (is_dir($dir)) {
    $files = scandir($dir);
    echo "Files in directory: " . implode(', ', array_filter($files, function($f) { return $f !== '.' && $f !== '..'; })) . "\n";
}

// Test asset URL generation
echo "\nAsset URL: " . asset('storage/apartment-covers/kynfrnmON9fPygUmAcH1akbS99655E9h13dREbxz.png') . "\n";

echo "\n=== END TEST ===\n";
