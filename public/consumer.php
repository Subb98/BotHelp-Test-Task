<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/** @var array $config */

use Subb98\BotHelpTestTask\Queue\AmqpConsumer;

$queueName = $argv[1] ?? null;
if (null === $queueName) {
    throw new \InvalidArgumentException("The queueName parameter was not passed!");
}

AmqpConsumer::consume($queueName);
