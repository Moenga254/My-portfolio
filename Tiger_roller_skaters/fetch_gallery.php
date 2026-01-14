<?php
header('Content-Type: application/json');
require 'config.php'; // must set $db (mysqli)

if (!isset($db) || !($db instanceof mysqli)) {
    echo json_encode(['success'=>false,'error'=>'Database connection not available']);
    exit;
}

$res = $db->query("SELECT id, image_path FROM gallery ORDER BY created_at DESC");
$images = [];
if ($res) {
    while ($row = $res->fetch_assoc()) $images[] = $row;
}
echo json_encode(['success'=>true,'images'=>$images]);
$db->close();
exit;
?>