<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Websocket;

use Subb98\BotHelpTestTask\Queue\Interfaces\AmqpPublisherInterface;
use Subb98\BotHelpTestTask\Queue\MessageDto;
use Workerman\Worker;

class WebsocketServer
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly bool $useSsl,
        private readonly string $sslCert,
        private readonly string $sslKey,
        private readonly AmqpPublisherInterface $amqpPublisher
    ) {
    }

    public function startWebsocketServer(): void
    {
        $serverDsn = sprintf('websocket://%s:%d', $this->host, $this->port);

        $context = [];
        if (true === $this->useSsl) {
            $context = [
                'ssl' => [
                    'local_cert' => $this->sslCert,
                    'local_pk' => $this->sslKey,
                    'verify_peer' => false,
                ]
            ];
        }

        $wsServer = new Worker($serverDsn, $context);

        if (true === $this->useSsl) {
            $wsServer->transport = 'ssl';
        }

        $wsServer->onConnect = function ($connection) {
            echo "New connection\n";
        };

        $wsServer->onMessage = function ($connection, $data) {
            $dataArr = json_decode($data, true);
            $messageDto = new MessageDto($dataArr['client'], $dataArr['event']);
            $this->amqpPublisher->sendMessage($messageDto);
            $connection->send('Receive ' . $data);
        };

        $wsServer->onClose = function ($connection) {
            echo "Connection closed\n";
        };

        $wsServer::runAll();
    }
}
