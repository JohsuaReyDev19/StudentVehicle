<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode = $_POST['barcode'] ?? '';
    
    if (empty($barcode)) {
        echo json_encode(['status' => 'error', 'message' => 'No barcode provided']);
        exit;
    }

    // Check if vehicle exists
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle not found']);
        exit;
    }

    $vehicle = $result->fetch_assoc();

    // Log entry/exit
    $stmt = $conn->prepare("INSERT INTO logs (vehicle_id, action) VALUES (?, ?)");
    
    // Determine if vehicle is entering or exiting
    $lastLog = $conn->query("SELECT action FROM logs WHERE vehicle_id = {$vehicle['id']} ORDER BY timestamp DESC LIMIT 1");
    $action = 'entry';
    
    if ($lastLog->num_rows > 0) {
        $lastAction = $lastLog->fetch_assoc()['action'];
        $action = ($lastAction === 'entry') ? 'exit' : 'entry';
    }

    $stmt->bind_param("is", $vehicle['id'], $action);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'vehicle_type' => $vehicle['type'],
            'plate_number' => $vehicle['plate_number'],
            'owner_name' => $vehicle['owner_name'],
            'action' => $action,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>