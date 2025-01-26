// Handle registration form submission
$('#registrationForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'register_vehicle.php',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            const resultDiv = $('#registrationResult');
            if (response.status === 'success') {
                resultDiv.html(`
                    <div class="p-4 bg-green-50 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Registration Successful</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p>Vehicle registered successfully!</p>
                                    <p>Barcode: ${response.data.barcode}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                $('#registrationForm')[0].reset();
            } else {
                resultDiv.html(`
                    <div class="p-4 bg-red-50 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Registration Failed</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>${response.message}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
            resultDiv.removeClass('hidden');
        },
        error: function() {
            showError('Server error occurred during registration');
        }
    });
});

// Barcode scanner configuration
let scannerIsRunning = false;
let lastScannedCode = null;
let lastScannedTime = 0;
const SCAN_DELAY = 2000; // 2 seconds delay between scans

function startScanner() {
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector("#interactive"),
            constraints: {
                facingMode: "environment"
            },
        },
        decoder: {
            readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "upc_reader"],
            debug: {
                drawBoundingBox: false,
                showFrequency: false,
                drawScanline: false,
                showPattern: false
            }
        },
        frequency: 2 // Reduce scanning frequency
    }, function(err) {
        if (err) {
            console.error(err);
            showError('Failed to initialize camera. Please ensure camera permissions are granted.');
            return;
        }
        Quagga.start();
        scannerIsRunning = true;
        document.getElementById('interactive').classList.remove('hidden');
        document.getElementById('stopScannerBtn').classList.remove('hidden');
    });
    Quagga.onDetected(function(result) {
        const currentTime = new Date().getTime();
        const code = result.codeResult.code;
        
        // Check if this is a new code or if enough time has passed since last scan
        if (code && 
            (code !== lastScannedCode || 
             currentTime - lastScannedTime > SCAN_DELAY)) {
            
            lastScannedCode = code;
            lastScannedTime = currentTime;
            
            document.getElementById('barcodeInput').value = code;
            processBarcode();
            stopScanner();
        }
    });
}

function stopScanner() {
    if (scannerIsRunning) {
        Quagga.stop();
        scannerIsRunning = false;
        document.getElementById('interactive').classList.add('hidden');
        document.getElementById('stopScannerBtn').classList.add('hidden');
    }
}

// Handle barcode input

// Handle barcode input
function processBarcode() {
    const barcode = document.getElementById('barcodeInput').value;
    if (!barcode) return;
    $.ajax({
        url: 'process_scan.php',
        method: 'POST',
        data: { barcode: barcode },
        success: function(response) {
            if (response.status === 'success') {
                showScanResult(response.data);
                updateLogs();
                document.getElementById('barcodeInput').value = '';
            } else {
                showError(response.message);
            }
        },
        error: function() {
            showError('Server error occurred');
        }
    });
}

function showScanResult(data) {
    const resultDiv = document.getElementById('scanResult');
    resultDiv.innerHTML = `
        <div class="p-4 ${data.action === 'entry' ? 'bg-green-50' : 'bg-blue-50'} rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas ${data.action === 'entry' ? 'fa-sign-in-alt text-green-400' : 'fa-sign-out-alt text-blue-400'}"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium ${data.action === 'entry' ? 'text-green-800' : 'text-blue-800'}">
                        Vehicle ${data.action === 'entry' ? 'Entered' : 'Exited'}
                    </h3>
                    <div class="mt-2 text-sm ${data.action === 'entry' ? 'text-green-700' : 'text-blue-700'}">
                        <p>Type: ${data.vehicle_type}</p>
                        <p>Owner: ${data.owner_name}</p>
                        <p>Plate Number: ${data.plate_number}</p>
                        <p>Time: ${data.timestamp}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    resultDiv.classList.remove('hidden');
}

function showError(message) {
    const resultDiv = document.getElementById('scanResult');
    resultDiv.innerHTML = `
        <div class="p-4 bg-red-50 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>${message}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    resultDiv.classList.remove('hidden');
}

function updateLogs() {
    $.ajax({
        url: 'get_logs.php',
        method: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                const tbody = document.getElementById('logsTableBody');
                tbody.innerHTML = response.data.map(log => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.timestamp}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${log.owner_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${log.vehicle_type}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${log.plate_number}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${log.action === 'entry' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                                ${log.action === 'entry' ? 'Entry' : 'Exit'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
        }
    });
}

// Initial logs load
updateLogs();

// Handle Enter key press
document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        processBarcode();
    }
});

// Auto-refresh logs every 30 seconds
setInterval(updateLogs, 30000);

// Clean up scanner when page is unloaded
window.addEventListener('beforeunload', function() {
    if (scannerIsRunning) {
        stopScanner();
    }
});
