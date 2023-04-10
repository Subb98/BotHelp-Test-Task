<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Websocket;

use RuntimeException;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;

class WebsocketClient
{
    public function __construct(private readonly string $dsn)
    {
    }

    public function sendMessagesFromCsvFile(string $inputCsvFile): void
    {
        $worker = new Worker();
        $worker->onWorkerStart = function () use ($inputCsvFile) {
            $wsConnection = new AsyncTcpConnection($this->dsn);
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
