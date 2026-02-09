<?php
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->safeLoad();

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/src/Infrastructure/Database/Migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/src/Infrastructure/Database/Seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'name' => $_ENV['DB_NAME'] ?? 'agendapro',
            'user' => $_ENV['DB_USER'] ?? 'root',
            'pass' => $_ENV['DB_PASSWORD'] ?? 'root',
            'port' => $_ENV['DB_PORT'] ?? '8889',
            'charset' => 'utf8',
        ]

    ],
    'version_order' => 'creation'
];
