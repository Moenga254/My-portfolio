<?php
// dev debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require 'config.php'; // must set $db = new mysqli(...);

// quick sanity
if (!isset($db) || !($db instanceof mysqli)) {
    echo json_encode(['success'=>false,'error'=>'Database connection not available (check config.php)']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success'=>false,'error'=>'No file uploaded or upload error','code'=> $_FILES['image']['error'] ?? null]);
    exit;
}

$tmp = $_FILES['image']['tmp_name'];
$info = @getimagesize($tmp);
if (!$info) {
    echo json_encode(['success'=>false,'error'=>'Uploaded file is not a valid image']);
    exit;
}

$mime = $info['mime'];
$extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
if (!isset($extMap[$mime])) {
    echo json_encode(['success'=>false,'error'=>'Unsupported image type','mime'=>$mime]);
    exit;
}

$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) {
    if (!mkdir($uploadsDir, 0755, true)) {
        echo json_encode(['success'=>false,'error'=>'Failed to create uploads directory']);
        exit;
    }
}

// safe filename
$filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extMap[$mime];
$dest = $uploadsDir . '/' . $filename;

if (!move_uploaded_file($tmp, $dest)) {
    echo json_encode(['success'=>false,'error'=>'Failed to move uploaded file']);
    exit;
}

// store web path relative to this folder
$webPath = 'uploads/' . $filename;

$stmt = $db->prepare("INSERT INTO gallery (image_path) VALUES (?)");
if (!$stmt) {
    echo json_encode(['success'=>false,'error'=>'DB prepare failed','db_error'=>$db->error]);
    exit;
}
$stmt->bind_param('s', $webPath);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'path'=>$webPath,'id'=>$stmt->insert_id]);
} else {
    // cleanup file on DB failure
    @unlink($dest);
    echo json_encode(['success'=>false,'error'=>'DB insert failed','stmt_error'=>$stmt->error]);
}
$stmt->close();
$db->close();
exit;
?>