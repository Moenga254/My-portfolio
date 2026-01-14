<?php
session_start();
require 'config.php'; // must provide $db (mysqli)

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$parentName  = trim($input['parentName']  ?? '');
$parentID    = trim($input['parentID']    ?? '');
$location    = trim($input['location']    ?? '');
$numKids     = intval($input['numKids']   ?? 0);
$level       = trim($input['level']       ?? '');
$startDate   = !empty($input['startDate']) ? $input['startDate'] : null;
$endDate     = !empty($input['endDate']) ? $input['endDate'] : null;
$has_gears   = isset($input['has_gears']) ? intval($input['has_gears']) : 0;
$child_names = is_array($input['child_names']) ? json_encode(array_values($input['child_names'])) : null;

if ($parentName === '') {
    echo json_encode(['success' => false, 'error' => 'Parent name required']);
    exit;
}

// Insert into bookings table
$sql = "INSERT INTO bookings (parent_name, parent_id, location, num_kids, level, start_date, end_date, has_gears, child_names)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'DB prepare failed: ' . $db->error]);
    exit;
}

// Bind parameters: s=string, i=integer
// Order: parent_name, parent_id, location, num_kids, level, start_date, end_date, has_gears, child_names
$bind_ok = $stmt->bind_param(
    'sssisssis',
    $parentName,
    $parentID,
    $location,
    $numKids,
    $level,
    $startDate,
    $endDate,
    $has_gears,
    $child_names
);

if (!$bind_ok) {
    echo json_encode(['success' => false, 'error' => 'DB bind failed: ' . $stmt->error]);
    exit;
}

$exec_ok = $stmt->execute();
if ($exec_ok) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB insert failed: ' . $stmt->error]);
}

$stmt->close();
$db->close();
?>