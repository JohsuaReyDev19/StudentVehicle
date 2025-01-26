<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Vehicle Access System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #interactive.viewport {
            position: relative;
            width: 100%;
            height: 300px;
        }
        #interactive.viewport > canvas, #interactive.viewport > video {
            max-width: 100%;
            width: 100%;
        }
        canvas.drawing, canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-semibold text-gray-900">School Vehicle Access System</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Scan Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Scan Vehicle Barcode</h2>
                <div class="flex flex-col space-y-4">
                    <div class="flex space-x-4">
                        <input type="text" id="barcodeInput" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Scan or enter barcode..." autofocus>
                        <button onclick="processBarcode()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Process
                        </button>
                    </div>
                    <div class="flex space-x-4">
                        <button onclick="startScanner()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <i class="fas fa-camera mr-2"></i>Start Camera Scanner
                        </button>
                        <button onclick="stopScanner()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 hidden" id="stopScannerBtn">
                            <i class="fas fa-stop mr-2"></i>Stop Scanner
                        </button>
                    </div>
                    <div id="interactive" class="viewport hidden"></div>
                </div>
                
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
</body>
</html>