<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue;

class AmqpConsumer
{
    public readonly string $queue;

    public static function consume(string $queue)
    {
        $consumerTag = 'consumer';

        $connection = AmqpConnection::getInstance();
        $channel = $connection->channel();

        $channel->basic_consume(
            $queue,
            $consumerTag,
            false,
            false,
            false,
            false,
            function ($message) {
                echo "\n--------\n";
                echo $message->body;
                echo "\n--------\n";

                sleep(1); // Эмулируем задержку при обработке

                $message->ack();

                // Send a message with the string "quit" to cancel the consumer.
                if ($message->body === 'quit') {
                    $message->getChannel()->basic_cancel($message->getConsumerTag());
                }
            }
        );

        register_shutdown_function(
            function ($channel, $connection) {
                $channel->close();
                $connection->close();
            },
            $channel,
            $connection
        );

        $channel->consume();
    }
}
