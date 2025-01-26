<?php
require_once 'config.php';

header('Content-Type: application/json');

$query = "
    SELECT 
        l.timestamp,
        l.action,
        v.type as vehicle_type,
        v.plate_number,
        v.owner_name
    FROM logs l
    JOIN vehicles v ON l.vehicle_id = v.id
    WHERE DATE(l.timestamp) = CURDATE()
    ORDER BY l.timestamp DESC
";

$result = $conn->query($query);
$logs = [];

while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $logs]);
?>