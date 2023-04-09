<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue;

readonly class MessageDto
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
