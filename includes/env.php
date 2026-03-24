<?php
// Load environment variables from .env file
$envFilePath = __DIR__ . '/../.env';
if (file_exists($envFilePath)) {
    $envVariables = parse_ini_file($envFilePath, false, INI_SCANNER_RAW);
    if ($envVariables) {
        foreach ($envVariables as $key => $value) {
            $_ENV[$key] = trim($value, '"\'');
        }
    }
}
