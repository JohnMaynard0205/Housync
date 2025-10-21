<?php
/**
 * RFID API Test Script for Railway Deployment
 * Tests all RFID endpoints to ensure they're working correctly
 */

class RfidApiTester
{
    private $baseUrl;
    private $testResults = [];

    public function __construct($baseUrl = 'https://housync.up.railway.app')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        echo "RFID API Tester for: {$this->baseUrl}\n";
        echo str_repeat('=', 50) . "\n\n";
    }

    /**
     * Make HTTP request
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->baseUrl . $endpoint;
        
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => [
                    'Content-Type: application/json',
                    'User-Agent: RfidApiTester/1.0'
                ],
                'content' => $data ? json_encode($data) : null,
                'timeout' => 30
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        $httpCode = 200;
        
        if (isset($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                    $httpCode = (int)$matches[1];
                    break;
                }
            }
        }

        return [
            'success' => $response !== false,
            'response' => $response,
            'http_code' => $httpCode,
            'data' => $response ? json_decode($response, true) : null
        ];
    }

    /**
     * Test system info endpoint
     */
    public function testSystemInfo()
    {
        echo "Testing System Info...\n";
        $result = $this->makeRequest('/api/system-info');
        
        if ($result['success'] && $result['data']) {
            echo "✓ System Info: PASSED\n";
            echo "  PHP Version: " . ($result['data']['php_version'] ?? 'Unknown') . "\n";
            echo "  Laravel Version: " . ($result['data']['laravel_version'] ?? 'Unknown') . "\n";
            echo "  Database: " . ($result['data']['database_connected'] ? 'Connected' : 'Disconnected') . "\n";
            $this->testResults['system_info'] = true;
        } else {
            echo "✗ System Info: FAILED\n";
            echo "  Error: Could not connect to system\n";
            $this->testResults['system_info'] = false;
        }
        echo "\n";
    }

    /**
     * Test RFID connection endpoint
     */
    public function testRfidConnection()
    {
        echo "Testing RFID Connection...\n";
        $result = $this->makeRequest('/api/rfid/test', 'POST');
        
        if ($result['success'] && $result['data']) {
            echo "✓ RFID Connection: PASSED\n";
            echo "  Message: " . ($result['data']['message'] ?? 'No message') . "\n";
            echo "  Endpoint: " . ($result['data']['endpoint'] ?? 'Unknown') . "\n";
            $this->testResults['rfid_connection'] = true;
        } else {
            echo "✗ RFID Connection: FAILED\n";
            echo "  HTTP Code: " . $result['http_code'] . "\n";
            $this->testResults['rfid_connection'] = false;
        }
        echo "\n";
    }

    /**
     * Test card verification with dummy data
     */
    public function testCardVerification()
    {
        echo "Testing Card Verification...\n";
        $testCardUid = 'TEST123456';
        
        $result = $this->makeRequest('/api/rfid/verify', 'POST', [
            'card_uid' => $testCardUid
        ]);
        
        if ($result['success'] && $result['data']) {
            echo "✓ Card Verification: PASSED\n";
            echo "  Card UID: " . ($result['data']['card_uid'] ?? 'Unknown') . "\n";
            echo "  Access Granted: " . ($result['data']['access_granted'] ? 'Yes' : 'No') . "\n";
            if (isset($result['data']['denial_reason'])) {
                echo "  Denial Reason: " . $result['data']['denial_reason'] . "\n";
            }
            $this->testResults['card_verification'] = true;
        } else {
            echo "✗ Card Verification: FAILED\n";
            echo "  HTTP Code: " . $result['http_code'] . "\n";
            $this->testResults['card_verification'] = false;
        }
        echo "\n";
    }

    /**
     * Test direct card scanning endpoint
     */
    public function testDirectScan()
    {
        echo "Testing Direct Card Scan...\n";
        $testCardUid = 'SCAN789ABC';
        
        $result = $this->makeRequest('/api/rfid/scan/direct', 'POST', [
            'cardUID' => $testCardUid,
            'timestamp' => date('Y-m-d H:i:s'),
            'reader_location' => 'test_location',
            'device_id' => 'test_device'
        ]);
        
        if ($result['success'] && $result['data']) {
            echo "✓ Direct Card Scan: PASSED\n";
            echo "  Card UID: " . ($result['data']['card_uid'] ?? 'Unknown') . "\n";
            echo "  Card Status: " . ($result['data']['card_status'] ?? 'Unknown') . "\n";
            echo "  Message: " . ($result['data']['message'] ?? 'No message') . "\n";
            $this->testResults['direct_scan'] = true;
        } else {
            echo "✗ Direct Card Scan: FAILED\n";
            echo "  HTTP Code: " . $result['http_code'] . "\n";
            $this->testResults['direct_scan'] = false;
        }
        echo "\n";
    }

    /**
     * Test latest card UID endpoint
     */
    public function testLatestCardUid()
    {
        echo "Testing Latest Card UID...\n";
        $result = $this->makeRequest('/api/rfid/latest-uid');
        
        if ($result['success']) {
            if ($result['data'] && isset($result['data']['card_uid'])) {
                echo "✓ Latest Card UID: PASSED\n";
                echo "  Card UID: " . $result['data']['card_uid'] . "\n";
                echo "  Scanned At: " . ($result['data']['scanned_at'] ?? 'Unknown') . "\n";
                $this->testResults['latest_uid'] = true;
            } else {
                echo "⚠ Latest Card UID: NO DATA\n";
                echo "  Message: No cards have been scanned yet\n";
                $this->testResults['latest_uid'] = true; // This is expected if no cards scanned
            }
        } else {
            echo "✗ Latest Card UID: FAILED\n";
            echo "  HTTP Code: " . $result['http_code'] . "\n";
            $this->testResults['latest_uid'] = false;
        }
        echo "\n";
    }

    /**
     * Test recent logs endpoint
     */
    public function testRecentLogs()
    {
        echo "Testing Recent Logs...\n";
        $result = $this->makeRequest('/api/rfid/recent-logs');
        
        if ($result['success'] && $result['data']) {
            echo "✓ Recent Logs: PASSED\n";
            echo "  Logs Count: " . count($result['data']['logs'] ?? []) . "\n";
            if (!empty($result['data']['logs'])) {
                $latestLog = $result['data']['logs'][0];
                echo "  Latest Log: " . ($latestLog['card_uid'] ?? 'Unknown') . " at " . ($latestLog['access_time_human'] ?? 'Unknown') . "\n";
            }
            $this->testResults['recent_logs'] = true;
        } else {
            echo "✗ Recent Logs: FAILED\n";
            echo "  HTTP Code: " . $result['http_code'] . "\n";
            $this->testResults['recent_logs'] = false;
        }
        echo "\n";
    }

    /**
     * Run all tests
     */
    public function runAllTests()
    {
        $this->testSystemInfo();
        $this->testRfidConnection();
        $this->testCardVerification();
        $this->testDirectScan();
        $this->testLatestCardUid();
        $this->testRecentLogs();
        
        $this->showSummary();
    }

    /**
     * Show test summary
     */
    public function showSummary()
    {
        echo str_repeat('=', 50) . "\n";
        echo "TEST SUMMARY\n";
        echo str_repeat('=', 50) . "\n";
        
        $passed = 0;
        $total = count($this->testResults);
        
        foreach ($this->testResults as $test => $result) {
            $status = $result ? '✓ PASSED' : '✗ FAILED';
            $testName = ucwords(str_replace('_', ' ', $test));
            echo sprintf("%-20s: %s\n", $testName, $status);
            if ($result) $passed++;
        }
        
        echo str_repeat('-', 50) . "\n";
        echo sprintf("Total: %d/%d tests passed\n", $passed, $total);
        
        if ($passed === $total) {
            echo "\n🎉 All tests passed! Your RFID API is working correctly.\n";
            echo "You can now run ESP32Reader.php to start scanning cards.\n";
        } else {
            echo "\n⚠ Some tests failed. Check your Railway deployment and try again.\n";
        }
        
        echo "\nNext steps:\n";
        echo "1. Connect your ESP32 RFID reader\n";
        echo "2. Run: php ESP32Reader.php --port=COM3\n";
        echo "3. Access web interface: {$this->baseUrl}/landlord/security\n";
    }
}

// Run the tests
if (php_sapi_name() === 'cli') {
    $url = $argv[1] ?? 'https://housync.up.railway.app';
    $tester = new RfidApiTester($url);
    $tester->runAllTests();
} else {
    echo "This script must be run from command line\n";
    echo "Usage: php test-rfid-api.php [url]\n";
    echo "Example: php test-rfid-api.php https://housync.up.railway.app\n";
}
?>
