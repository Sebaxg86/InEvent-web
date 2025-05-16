<?php
// ======= Define the env() Function =======
// This function retrieves environment variables from a .env file located in the project root.
// It caches the variables in a static array for improved performance.
function env($key, $default = null) {
    static $vars = null;

    // ======= Load Environment Variables from the .env File if Not Already Loaded =======
    // The .env file is expected to be one directory up from the config folder.
    if ($vars === null) {
        $vars = [];
        // ======= Read All Lines from the .env File =======
        // FILE_IGNORE_NEW_LINES removes newline characters.
        // FILE_SKIP_EMPTY_LINES ignores empty lines.
        $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // ======= Skip Lines That Are Comments =======
            // Lines starting with '#' are treated as comments and are ignored.
            if (strpos(trim($line), '#') === 0) continue;
            // ======= Split the Line into Variable Name and Value =======
            // The line is split by the first '=' character.
            [$name, $value] = explode('=', $line, 2);
            // ======= Trim and Store the Variable =======
            $vars[trim($name)] = trim($value);
        }
    }

    // ======= Return the Value of the Requested Environment Variable =======
    // If the environment variable is not found, return the default value.
    return $vars[$key] ?? $default;
}