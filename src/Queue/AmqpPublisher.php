<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue;

use PhpAmqpLib\Message\AMQPMessage;
use Subb98\BotHelpTestTask\Queue\Interfaces\AmqpPublisherInterface;
use Subb98\BotHelpTestTask\Queue\Interfaces\MessageDtoInterface;

class AmqpPublisher implements AmqpPublisherInterface
{
    private static string $exchangerName = 'ch-router';
    private static string $exchangerType = 'x-consistent-hash';

    public static function init(): void
    {
        $connection = AmqpConnection::getInstance();
        $channel = $connection->channel();

        $channel->exchange_declare(self::$exchangerName, self::$exchangerType, false, true, false);
        for ($i = 1; $i <= 100; $i++) {
            $queueName = 'ch' . $i;
            $channel->queue_declare($queueName, false, true, false, false);
            $channel->queue_bind($queueName, self::$exchangerName, 1);
        }

        $channel->close();
        $connection->close();
    }

    /**
     * @param MessageDtoInterface $messageDto
     * @return void
     * @throws \Exception
     */
    public function sendMessage(MessageDtoInterface $messageDto): void
    {
        $connection = AmqpConnection::getInstance();
        $channel = $connection->channel();

        $message = new AMQPMessage(serialize($messageDto), array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, self::$exchangerName, $messageDto->routingKey);

        $channel->close();
        $connection->close();
    }
}
