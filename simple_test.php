<?php
/**
 * Simple COM Port Test - No exclusive access
 */

echo "Testing COM3 accessibility...\n";

// Try different approaches to access COM3
$methods = [
    'fopen COM3 read-only' => function() {
        $handle = fopen("COM3", 'rb');
        if ($handle) {
            echo "✅ Can open COM3 for reading\n";
            fclose($handle);
            return true;
        }
        return false;
    },
    'fopen COM3 read-write' => function() {
        $handle = fopen("COM3", 'r+b');
        if ($handle) {
            echo "✅ Can open COM3 for read/write\n";
            fclose($handle);
            return true;
        }
        return false;
    },
    'mode command' => function() {
        exec("mode COM3:", $output, $returnVar);
        if ($returnVar === 0) {
            echo "✅ Mode command works\n";
            return true;
        }
        echo "❌ Mode command failed (return code: $returnVar)\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
        return false;
    }
];

foreach ($methods as $name => $method) {
    echo "\nTesting: $name\n";
    try {
        $result = $method();
        if (!$result) {
            echo "❌ Failed: $name\n";
        }
    } catch (Exception $e) {
        echo "❌ Exception in $name: " . $e->getMessage() . "\n";
    }
}

echo "\n=== RECOMMENDATION ===\n";
echo "1. Close Arduino IDE Serial Monitor completely\n";
echo "2. Try: php ESP32Reader.php --port=COM3\n";
echo "3. If still fails, disconnect/reconnect ESP32 USB cable\n";
?>
