# GCD Game

## Описание
GCD Game — это консольная игра, в которой пользователю предлагается вычислить наибольший общий делитель (НОД) двух случайных чисел. Игра предназначена для запуска через командную строку и реализована с использованием PHP.

## Установка

### 1. Установка через Composer
```sh
composer require danilnomad/gcd
```

### 2. Глобальная установка
```sh
composer global require danilanomad/gcd
```

## Использование
После установки можно запустить игру командой:
```sh
gcd
```

## Требования
- PHP 8.0+
- Composer

## Разработка
### Пространства имен
Проект использует пространства имен для всех файлов:
- `danilanomad\GCD\Controller`
- `danilanomad\GCD\View`

### Автозагрузка
Проект настроен на автозагрузку через `composer.json`:
```json
"autoload": {
    "files": [
        "src/Controller.php",
        "src/View.php"
    ]
}
```
После добавления файлов нужно выполнить команду:
```sh
composer dump-autoload -o
```

## Структура проекта
```
GCD/
├── bin/
│   ├── gcd.php  # Запускной скрипт игры
│
├── src/
│   ├── Controller.php  # Логика игры
│   ├── View.php  # Вывод информации
│
├── vendor/  # Устанавливаемые зависимости (игнорируется в Git)
├── composer.json  # Файл зависимостей Composer
├── README.md  # Данный файл
```
## Кодстайл
Проект следует стандартам PSR-1 и PSR-12. Для проверки используйте:
```
vendor/bin/phpcs --standard=PSR12 src/
```

## Публикация на Packagist
Проект опубликован на [Packagist](https://packagist.org/packages/danilanomad/gcd). Чтобы установить его, используйте:
```
composer require danilanomad/gcd
```

## Лицензия
Этот проект распространяется под лицензией MIT.

