<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue\Interfaces;

interface AmqpPublisherInterface
{
    public function sendMessage(MessageDtoInterface $messageDto): void;
}
