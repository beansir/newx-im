<?php
$config = [
    'app' => require __DIR__ . '/app.php',
    'component' => require __DIR__ . '/components.php',
    'server' => require __DIR__ . '/server.php',
];

$config['database'] = IS_LINUX ? require __DIR__ . '/databases.php' : require __DIR__ . '/databases-dev.php';

return $config;