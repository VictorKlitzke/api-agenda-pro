<?php 

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../../');
$dotenv->safeLoad();



return [
    "connect" => [
        "db.agendapro" => [
            "driver" => "pdo_mysql",
            "host" => getenv('DB_HOST'),
            "port" => getenv('DB_PORT'),
            "dbname" => getenv('DB_NAME'),
            "user" => getenv('DB_USER'),
            "password" => getenv('DB_PASSWORD'),
            "charset" => "utf8mb4",
        ]
    ]
];