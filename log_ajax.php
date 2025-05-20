<?php
require 'db.php';

$timestamp = $_POST['timestamp'] ?? null;
$browser = $_POST['browser'] ?? '';
$lat = $_POST['lat'] ?? null;
$lon = $_POST['lon'] ?? null;

if ($timestamp && $browser) {
    $stmt = $pdo->prepare("INSERT INTO ajax_logs (timestamp, browser, latitude, longitude) VALUES (?, ?, ?, ?)");
    $stmt->execute([$timestamp, $browser, $lat, $lon]);
}
?>
