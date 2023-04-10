<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue;

use Subb98\BotHelpTestTask\Queue\Interfaces\MessageDtoInterface;

readonly class MessageDto implements MessageDtoInterface
{
    public readonly int $client;
    public readonly int $event;
    public readonly int $routingKey;

    public function __construct(int $client, int $event, ?int $routingKey = null)
    {
        $this->client = $client;
        $this->event = $event;

        if ($routingKey !== null) {
            $this->routingKey = $routingKey;
        } else {
            $this->routingKey = $client;
        }
    }
}
