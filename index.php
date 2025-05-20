<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Пошук фільмів</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f4f4;
        padding: 40px;
        color: #333;
    }

    h1 {
        text-align: center;
        color: #222;
    }

    form {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    label {
        display: inline-block;
        margin-bottom: 6px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="number"],
    select {
        padding: 8px;
        margin: 5px 0 15px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
        box-sizing: border-box;
    }

    button {
        padding: 10px 20px;
        background-color: #0069d9;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }

    button:hover {
        background-color: #0053b3;
    }

    .results {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0,0,0,0.05);
    }

    ul {
        list-style-type: disc;
        padding-left: 20px;
    }

    li {
        padding: 4px 0;
    }

    @media (min-width: 768px) {
        form {
            width: 60%;
            margin: 0 auto 20px auto;
        }

        .results {
            width: 60%;
            margin: 0 auto;
        }
    }
</style>
    <script>
    function sendLogData() {
        if (!navigator.geolocation) return;

        navigator.geolocation.getCurrentPosition(function(position) {
            const timestamp = new Date().toISOString();
            const browser = navigator.userAgent;
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            const params = `timestamp=${encodeURIComponent(timestamp)}&browser=${encodeURIComponent(browser)}&lat=${latitude}&lon=${longitude}`;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'log_ajax.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(params);
        });
    }

    function ajaxRequest(url, data, callback, responseType = 'text') {
        sendLogData(); // <--- Додаємо логування тут
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.responseType = responseType;
        xhr.onload = function () {
            if (xhr.status === 200) {
                callback(xhr.response);
            }
        };
        xhr.send(data);
    }

    function searchByGenre(event) {
        event.preventDefault();
        const genreId = document.getElementById('genre').value;
        if (!genreId) return;

        ajaxRequest('search_genre.php', 'genre=' + genreId, function(response) {
            document.getElementById('results-genre').innerHTML = response;
        });
    }



    function searchByActor(event) {
    event.preventDefault();
    const actor = document.getElementById('actor').value;
    if (!actor) return;

    const callbackName = 'handleActorResults';
    const script = document.createElement('script');
    script.src = `search_actor.php?actor=${encodeURIComponent(actor)}&callback=${callbackName}`;
    document.body.appendChild(script);
}

function handleActorResults(data) {
    let html = '<ul>';
    if (data.length) {
        data.forEach(film => {
            html += `<li>${film.name} (${film.date})</li>`;
        });
    } else {
        html += '<li>Немає результатів.</li>';
    }
    html += '</ul>';
    document.getElementById('results-actor').innerHTML = html;
}



    function searchByYear(event) {
        event.preventDefault();
        const from = document.getElementById('year_from').value;
        const to = document.getElementById('year_to').value;

        ajaxRequest('search_year.php', 'from=' + from + '&to=' + to, function(xml) {
            const items = xml.getElementsByTagName('film');
            let html = '<ul>';
            for (let i = 0; i < items.length; i++) {
                html += `<li>${items[i].getElementsByTagName('name')[0].textContent} (${items[i].getElementsByTagName('date')[0].textContent})</li>`;
            }
            html += '</ul>';
            document.getElementById('results-year').innerHTML = html;
        }, 'document');
    }
    </script>
</head>
<body>
    <h1>Пошук фільмів</h1>

    <!-- Жанр -->
    <form onsubmit="searchByGenre(event)">
        <label>Оберіть жанр:</label>
        <select id="genre" required>
            <option value="">-- оберіть --</option>
            <?php
            $genres = $pdo->query("SELECT * FROM genre");
            foreach ($genres as $genre) {
                echo "<option value='{$genre['ID_Genre']}'>{$genre['title']}</option>";
            }
            ?>
        </select>
        <button type="submit">Пошук</button>
    </form>
    <div class="results" id="results-genre"></div>

    <!-- Актор -->
    <form onsubmit="searchByActor(event)">
        <label>Ім’я актора:</label>
        <input type="text" id="actor" required>
        <button type="submit">Пошук</button>
    </form>
    <div class="results" id="results-actor"></div>

    <!-- Рік -->
    <form onsubmit="searchByYear(event)">
        <label>Рік (від / до):</label>
        <input type="number" id="year_from" required>
        <input type="number" id="year_to" required>
        <button type="submit">Пошук</button>
    </form>
    <div class="results" id="results-year"></div>
</body>
</html>
