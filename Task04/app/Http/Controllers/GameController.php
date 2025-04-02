<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function index()
    {
        return view('game');
    }

    public function history()
    {
        $games = DB::table('games')->orderBy('date', 'desc')->get();
        return response()->json($games);
    }

    public function startGame(Request $request)
    {
        $playerName = $request->input('player_name', 'Игрок');
        $num1 = rand(10, 100);
        $num2 = rand(10, 100);
        $gcd = $this->gcd($num1, $num2);

        $gameId = DB::table('games')->insertGetId([
            'player_name'   => $playerName,
            'date'          => now(),
            'number1'       => $num1,
            'number2'       => $num2,
            'gcd'           => $gcd,
            'result'        => '',
            'player_answer' => 'Не дан',
        ]);

        return response()->json([
            'game_id' => $gameId,
            'num1'    => $num1,
            'num2'    => $num2,
        ]);
    }

    public function step(Request $request, $id)
    {
        $answer = intval($request->input('answer', 0));
        $playerName = $request->input('player_name', 'Игрок');

        $game = DB::table('games')->where('id', $id)->first();

        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        $correct = ($game->gcd == $answer) ? "correct" : "incorrect";

        DB::table('games')->where('id', $id)->update([
            'result'        => $correct,
            'player_answer' => $answer,
            'player_name'   => $playerName,
        ]);

        $message = ($correct === "correct")
            ? "✅ Верно!"
            : "❌ Неверно.";
        $message .= " НОД для чисел {$game->number1} и {$game->number2} равен {$game->gcd}.";

        return response()->json([
            'result'         => $correct,
            'correct_answer' => $game->gcd,
            'message'        => $message,
            'game_id'        => $id,
        ]);
    }

    private function gcd($a, $b)
    {
        while ($b) {
            $a %= $b;
            list($a, $b) = [$b, $a];
        }
        return abs($a);
    }
}
