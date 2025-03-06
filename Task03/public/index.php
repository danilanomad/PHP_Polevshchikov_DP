<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$dbPath = __DIR__ . '/../db/game.db';
$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player_name TEXT NOT NULL,
    date TEXT NOT NULL,
    number1 INTEGER NOT NULL,
    number2 INTEGER NOT NULL,
    gcd INTEGER NOT NULL,
    result TEXT NOT NULL,
    player_answer INTEGER NOT NULL
);");

function gcd($a, $b) {
    while ($b) {
        $a %= $b;
        list($a, $b) = [$b, $a];
    }
    return abs($a);
}
$app->get('/', function (Request $request, Response $response) {
    $file = __DIR__ . '/index.html';
    if (file_exists($file)) {
        $response->getBody()->write(file_get_contents($file));
        return $response->withHeader('Content-Type', 'text/html');
    }
    return $response->withStatus(404)->getBody()->write('404 Not Found');
});
$app->get('/games', function (Request $request, Response $response) use ($pdo) {
    $stmt = $pdo->query("SELECT * FROM games ORDER BY date DESC");
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($games));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/games/{id}', function (Request $request, Response $response, $args) use ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$args['id']]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($game) {
        $response->getBody()->write(json_encode($game));
    } else {
        return $response->withStatus(404)->getBody()->write(json_encode(["error" => "Game not found"]));
    }
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/games', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    $player_name = $data['player_name'] ?? 'Игрок';
    $num1 = rand(10, 100);
    $num2 = rand(10, 100);
    $gcd = gcd($num1, $num2);

    $stmt = $pdo->prepare("INSERT INTO games (player_name, date, number1, number2, gcd, result, player_answer) 
                           VALUES (?, datetime('now'), ?, ?, ?, '', 'Не дан')");
    $stmt->execute([$player_name, $num1, $num2, $gcd]);

    $gameId = $pdo->lastInsertId();
    $response->getBody()->write(json_encode(["game_id" => $gameId, "num1" => $num1, "num2" => $num2]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/step/{id}', function (Request $request, Response $response, $args) use ($pdo) {
    $game_id = (int)$args['id'];
    $data = $request->getParsedBody();
    $answer = intval($data['answer'] ?? 0);
    $player_name = $data['player_name'] ?? 'Игрок';

    $stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
    $stmt->execute([$game_id]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        return $response->withStatus(404)->getBody()->write(json_encode(["error" => "Game not found"]));
    }

    $correct = ($game['gcd'] == $answer) ? "correct" : "incorrect";
    
    $stmt = $pdo->prepare("UPDATE games SET result = ?, player_answer = ?, player_name=? WHERE id = ?");
    $stmt->execute([$correct, $answer, $player_name, $game_id]);

    $response->getBody()->write(json_encode([
        "result" => $correct,
        "correct_answer" => $game['gcd'],
        "message" => ($correct === "correct" ? "✅ Верно!" : "❌ Неверно.") . " НОД для чисел {$game['number1']} и {$game['number2']} равен {$game['gcd']}.",
        "game_id" => $game_id
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
