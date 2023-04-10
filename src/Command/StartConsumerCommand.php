<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Command;

use Subb98\BotHelpTestTask\Queue\AmqpConsumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartConsumerCommand extends Command
{
    protected static $defaultName = 'app:start-consumer';
    protected static $defaultDescription = 'Starts a consumer for the specified queue.';

    protected function configure(): void
    {
        $this->addArgument('queueName', InputArgument::REQUIRED, 'Queue name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        AmqpConsumer::consume($input->getArgument('queueName'));
        return Command::SUCCESS;
    }
}
