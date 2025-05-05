<?php
function env($key, $default = null) {
    static $vars = null;

    if ($vars === null) {
        $vars = [];
        $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$name, $value] = explode('=', $line, 2);
            $vars[trim($name)] = trim($value);
        }
    }

    return $vars[$key] ?? $default;
}