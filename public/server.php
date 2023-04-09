<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/** @var array $config */

use Workerman\Worker;

$serverDsn = sprintf('websocket://%s:%d', $config['ws_server_host'], $config['ws_server_port']);

$context = [];
if (true === $config['ws_server_use_ssl']) {
    $context = [
        'ssl' => [
            'local_cert' => $config['ws_server_ssl_cert'],
            'local_pk' => $config['ws_server_ssl_key'],
            'verify_peer' => false,
        ]
    ];
}

$wsServer = new Worker($serverDsn, $context);

if (true === $config['ws_server_use_ssl']) {
    $wsServer->transport = 'ssl';
}

$wsServer->onConnect = function ($connection) {
    echo "New connection\n";
};

$wsServer->onMessage = function ($connection, $data) {
    $connection->send('Hello ' . $data);
};

$wsServer->onClose = function ($connection) {
    echo "Connection closed\n";
};

Worker::runAll();
