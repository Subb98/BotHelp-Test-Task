<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Websocket;

use RuntimeException;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;

class WebsocketClient
{
    public static function sendMessagesFromCsvFile(string $inputCsvFile): void
    {
        $worker = new Worker(); // TODO: исп. внедрение зависимости
        $worker->onWorkerStart = function () use ($inputCsvFile) {
            // TODO: исп. внедрение зависимости
            $wsConnection = new AsyncTcpConnection('ws://php:8000'); // TODO: забирать из конфига
            $wsConnection->onConnect = function ($connection) use ($inputCsvFile) {
                $fh = fopen($inputCsvFile, 'r');
                if (false === $fh) {
                    throw new RuntimeException("Failed to read file '$inputCsvFile'");
                }

                while (($data = fgetcsv($fh, 100, ",")) !== false) {
                    $row = $data[0] ?? '';
                    $connection->send($row);
                }

                fclose($fh);
            };

            $wsConnection->onMessage = function ($connection, $data) {
                echo "Recv: $data\n";
            };

            $wsConnection->onError = function ($connection, $code, $msg) {
                echo "Error: $msg\n";
            };

            $wsConnection->onClose = function ($connection) {
                echo "Connection closed\n";
            };

            $wsConnection->connect();
        };

        $worker::runAll();
    }
}
