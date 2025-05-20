<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/javascript');

// Підключення до БД (тимчасово без db.php для тесту)
$pdo = new PDO('mysql:host=localhost;dbname=lb_pdo_films;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Отримання параметрів
$actor = $_GET['actor'] ?? '';
$callback = $_GET['callback'] ?? 'callback';

// Захист callback
if (!preg_match('/^[a-zA-Z0-9_]+$/', $callback)) {
    echo '/* Invalid callback */';
    exit;
}

if (!$actor) {
    echo $callback . '([]);';
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT film.name, film.date 
        FROM film 
        JOIN film_actor ON film.ID_FILM = film_actor.FID_Film 
        JOIN actor ON actor.ID_Actor = film_actor.FID_Actor 
        WHERE actor.name LIKE ?
    ");
    $stmt->execute(["%$actor%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo $callback . '(' . json_encode($results) . ');';
} catch (Exception $e) {
    echo "/* ERROR: " . addslashes($e->getMessage()) . " */";
}
