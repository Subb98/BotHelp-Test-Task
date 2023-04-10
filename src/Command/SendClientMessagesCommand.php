<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Command;

use Subb98\BotHelpTestTask\Websocket\WebsocketClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendClientMessagesCommand extends Command
{
    protected static $defaultName = 'app:send-client-messages';
    protected static $defaultDescription = 'Sends the generated messages to the server.';

    private readonly string $inputCsvFile;

    public function __construct(string $inputCsvFile)
    {
        $this->inputCsvFile = $inputCsvFile;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('inputCsvFile', InputArgument::REQUIRED, 'Input CSV file');
        $this->addArgument('optcmd', InputArgument::OPTIONAL, 'Command for WebsocketClient');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $websocketClient = new WebsocketClient('ws://php:8000');
        $websocketClient->sendMessagesFromCsvFile($this->inputCsvFile);
        return Command::SUCCESS;
    }
}
