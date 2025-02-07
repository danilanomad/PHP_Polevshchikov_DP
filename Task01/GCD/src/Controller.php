<?php

namespace danilanomad\GCD;

use cli;

class Controller
{
    public function startGame()
    {
        $num1 = rand(1, 100);
        $num2 = rand(1, 100);
        echo "Найди наибольший общий делитель (НОД) для чисел $num1 и $num2:\n";

        $answer = cli\prompt("Введите ваш ответ");

        $correctAnswer = $this->getGCD($num1, $num2);

        if ($answer == $correctAnswer) {
            echo "Правильно! НОД чисел $num1 и $num2: $correctAnswer\n";
        } else {
            echo "Неправильно. Правильный ответ: $correctAnswer\n";
        }
    }

    private function getGCD($a, $b)
    {
        while ($b) {
            $a %= $b;
            $a ^= $b ^= $a ^= $b;
        }
        return $a;
    }
}
