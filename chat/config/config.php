<?php
$config = [
    'web' => require __DIR__ . '/web.php',
    'component' => require __DIR__ . '/components.php'
];

$config['database'] = IS_LINUX ? require __DIR__ . '/databases.php' : require __DIR__ . '/databases-dev.php';

return $config;