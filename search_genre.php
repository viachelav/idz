<?php
require 'db.php';

if (!empty($_POST['genre'])) {
    $stmt = $pdo->prepare("
        SELECT name, date 
        FROM film 
        JOIN film_genre ON film.ID_FILM = film_genre.FID_Film 
        WHERE film_genre.FID_Genre = ?
    ");
    $stmt->execute([$_POST['genre']]);

    if ($stmt->rowCount()) {
        echo "<ul>";
        foreach ($stmt as $row) {
            echo "<li>{$row['name']} ({$row['date']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Немає результатів.</p>";
    }
}
