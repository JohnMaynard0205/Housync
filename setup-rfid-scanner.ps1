# RFID Scanner Setup for Railway Project
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "RFID Scanner Setup for Railway Project" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check available COM ports
Write-Host "Checking available COM ports..." -ForegroundColor Yellow
try {
    $ports = [System.IO.Ports.SerialPort]::GetPortNames()
    if ($ports.Count -gt 0) {
        Write-Host "Available COM ports:" -ForegroundColor Green
        foreach ($port in $ports) {
            Write-Host "  - $port" -ForegroundColor White
        }
    } else {
        Write-Host "No COM ports detected. Common ports: COM1, COM3, COM7, COM8" -ForegroundColor Yellow
    }
} catch {
    Write-Host "Could not detect COM ports. Common ports: COM1, COM3, COM7, COM8" -ForegroundColor Yellow
}
Write-Host ""

# Test Railway connection
Write-Host "Testing connection to Railway app..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "https://housync.up.railway.app/api/system-info" -Method Get -TimeoutSec 10
    Write-Host "✓ Railway app is online and responding" -ForegroundColor Green
    Write-Host "  PHP Version: $($response.php_version)" -ForegroundColor White
    Write-Host "  Laravel Version: $($response.laravel_version)" -ForegroundColor White
} catch {
    Write-Host "✗ Could not connect to Railway app" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "RFID Scanner Usage Instructions:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "1. Connect your ESP32 RFID reader to a USB port" -ForegroundColor White
Write-Host "2. Identify the COM port from the list above" -ForegroundColor White
Write-Host "3. Run the scanner with:" -ForegroundColor White
Write-Host ""
Write-Host "   php ESP32Reader.php --port=COM3 --url=https://housync.up.railway.app" -ForegroundColor Green
Write-Host ""
Write-Host "4. Replace COM3 with your actual COM port" -ForegroundColor White
Write-Host ""

Write-Host "Available options:" -ForegroundColor Yellow
Write-Host "  --port=COMx    Serial port (default: COM3)" -ForegroundColor White
Write-Host "  --url=URL      Laravel base URL (default: https://housync.up.railway.app)" -ForegroundColor White
Write-Host "  --help, -h     Show help message" -ForegroundColor White
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Example Commands:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "For COM3: " -NoNewline -ForegroundColor White
Write-Host "php ESP32Reader.php --port=COM3" -ForegroundColor Green
Write-Host "For COM7: " -NoNewline -ForegroundColor White
Write-Host "php ESP32Reader.php --port=COM7" -ForegroundColor Green
Write-Host "For COM8: " -NoNewline -ForegroundColor White
Write-Host "php ESP32Reader.php --port=COM8" -ForegroundColor Green
Write-Host ""

Write-Host "The scanner will:" -ForegroundColor Yellow
Write-Host "- Connect to your ESP32 RFID reader" -ForegroundColor White
Write-Host "- Send card data to your Railway app" -ForegroundColor White
Write-Host "- Log access attempts in your database" -ForegroundColor White
Write-Host "- Support web-triggered card scanning" -ForegroundColor White
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Quick Test Commands:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Test ESP32Reader help:" -ForegroundColor White
Write-Host "php ESP32Reader.php --help" -ForegroundColor Green
Write-Host ""

Write-Host "Test API connection:" -ForegroundColor White
Write-Host "curl https://housync.up.railway.app/api/rfid/test" -ForegroundColor Green
Write-Host ""

Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
