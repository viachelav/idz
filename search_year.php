<?php
require 'db.php';

header('Content-Type: text/xml');

$from = $_POST['from'];
$to = $_POST['to'];

$stmt = $pdo->prepare("SELECT name, date FROM film WHERE YEAR(date) BETWEEN ? AND ?");
$stmt->execute([$from, $to]);

echo "<?xml version='1.0' encoding='UTF-8'?>";
echo "<films>";
foreach ($stmt as $film) {
    echo "<film>";
    echo "<name>" . htmlspecialchars($film['name']) . "</name>";
    echo "<date>" . htmlspecialchars($film['date']) . "</date>";
    echo "</film>";
}
echo "</films>";
