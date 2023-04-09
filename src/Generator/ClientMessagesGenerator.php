<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Generator;

use Generator;

class ClientMessagesGenerator
{
    public static function clientsWithEventsGenerator(int $clientsCount = 1000, int $eventsCount = 10000): Generator
    {
        $eventsPerClient = (int)ceil($eventsCount / $clientsCount);
        for ($client = 1; $client <= $clientsCount; $client++) {
            for ($event = 0; $event < $eventsPerClient; $event++) {
                $revent = random_int(1, 10);
                yield "{\"client\":$client,\"event\":$revent}";
            }
        }
    }
}
