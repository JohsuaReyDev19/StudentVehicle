<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Vehicle Access System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <!-- Registration Form -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Register New Vehicle</h2>
                <form id="registrationForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vehicle Type</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="car">Car</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plate Number</label>
                            <input type="text" name="plate_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                            <input type="text" name="owner_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Register Vehicle
                        </button>
                    </div>
                </form>
                <div id="registrationResult" class="mt-4 hidden"></div>
            </div>
        </main>
    </div>

    <script>
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
                                            <p>Barcode:</p>
                                            <svg id="barcode"></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                        // Generate barcode using JsBarcode
                        JsBarcode("#barcode", response.data.barcode, {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 2,
                            height: 40,
                            displayValue: true
                        });

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
                    alert('Server error occurred during registration');
                }
            });
        });
    </script>
</body>
</html>