<?php
/**
 * Simple .env file loader - beginner-friendly, no external packages!
 */

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments (lines starting with #)
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split into key and value
        list($key, $value) = explode('=', $line, 2);

        // Clean up whitespace
        $key = trim($key);
        $value = trim($value);

        // Define as constant if not already defined
        if (!defined($key)) {
            define($key, $value);
        }
    }

    return true;
}

// Load the .env file from the project root
loadEnv(__DIR__ . '/../.env');
