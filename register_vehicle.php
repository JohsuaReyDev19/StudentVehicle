<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $plate_number = $_POST['plate_number'] ?? '';
    $owner_name = $_POST['owner_name'] ?? '';
    
    if (empty($type) || empty($plate_number) || empty($owner_name)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Check if plate number already exists
    $stmt = $conn->prepare("SELECT id FROM vehicles WHERE plate_number = ?");
    $stmt->bind_param("s", $plate_number);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle with this plate number already exists']);
        exit;
    }

    // Generate unique barcode (timestamp + random number)
    $barcode = time() . rand(1000, 9999);

    // Insert new vehicle
    $stmt = $conn->prepare("INSERT INTO vehicles (barcode, type, plate_number, owner_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $barcode, $type, $plate_number, $owner_name);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'barcode' => $barcode,
                'type' => $type,
                'plate_number' => $plate_number,
                'owner_name' => $owner_name
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register vehicle']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>