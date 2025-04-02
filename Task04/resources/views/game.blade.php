<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>НОД Игра</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            background-color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
    <script>
        let gameId;
        let currentGameData = null; 

        function startGame() {
            let playerName = document.getElementById("player_name").value;
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("/games", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken 
                },
                body: JSON.stringify({ player_name: playerName })
            })
            .then(response => response.json())
            .then(data => {
                gameId = data.game_id;
                document.getElementById("num1").innerText = data.num1;
                document.getElementById("num2").innerText = data.num2;
                document.getElementById("game").style.display = 'block';
            });
        }

        function sendAnswer() {
            let answer = document.getElementById("answer").value;
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/step/${gameId}`, {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken 
                },
                body: JSON.stringify({ answer: answer, player_name: document.getElementById("player_name").value })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("result").innerText = data.message;

                document.getElementById("answer_button").style.display = 'none';
                document.getElementById("next_button").style.display = 'inline-block';

                currentGameData = data; 
            });
        }

        function nextRound() {
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/games`, {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken 
                },
                body: JSON.stringify({ player_name: document.getElementById("player_name").value })
            })
            .then(response => response.json())
            .then(data => {
                gameId = data.game_id;
                document.getElementById("num1").innerText = data.num1;
                document.getElementById("num2").innerText = data.num2;

                document.getElementById("next_button").style.display = 'none';
                document.getElementById("answer_button").style.display = 'inline-block';
                document.getElementById("result").innerText = ''; 
            });
        }

        function showHistory() {
            fetch('/games')
            .then(response => response.json())
            .then(data => {
                let historyTable = document.getElementById("history_table").getElementsByTagName('tbody')[0];
                historyTable.innerHTML = "";
                data.forEach(game => {
                    let row = historyTable.insertRow();
                    row.innerHTML = `
                        <td>${game.id}</td>
                        <td>${game.player_name}</td>
                        <td>${game.number1} и ${game.number2}</td>
                        <td>${game.gcd}</td>
                        <td>${game.result === 'correct' ? 'Верно' : 'Неверно'}</td>
                        <td>${game.player_answer}</td>
                        <td>${game.date}</td>
                    `;
                });
                document.getElementById("history").style.display = 'block';
            });
        }
    </script>
</head>
<body>
    <h1>Наибольший общий делитель</h1>
    <input type="text" id="player_name" placeholder="Введите имя">
    <button onclick="startGame()">Начать игру</button>

    <div id="game" style="display:none;">
        <p>Числа: <span id="num1"></span> и <span id="num2"></span></p>
        <input type="number" id="answer">
        <button onclick="sendAnswer()" id="answer_button">Ответить</button>
        <button onclick="nextRound()" id="next_button" style="display:none;">Следующий ход</button>
        <p id="result"></p>
    </div>

    <button onclick="showHistory()">Посмотреть историю игр</button>
    <div id="history" style="display:none;">
        <h2>История игр</h2>
        <table id="history_table">
            <thead>
                <tr>
                    <th>ID игры</th>
                    <th>Игрок</th>
                    <th>Числа</th>
                    <th>Правильный НОД</th>
                    <th>Результат</th>
                    <th>Ответ игрока</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</body>
</html>
