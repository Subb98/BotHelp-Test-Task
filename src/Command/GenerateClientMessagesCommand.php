<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Command;

use RuntimeException;
use Subb98\BotHelpTestTask\Generator\ClientMessagesGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateClientMessagesCommand extends Command
{
    protected static $defaultName = 'app:generate-client-messages';
    protected static $defaultDescription = 'Generates input data to be sent to the server.';

    private readonly string $outputCsvFile;
    private readonly int $clientsCount;
    private readonly int $eventsCount;

    public function __construct(
        string $outputCsvFile,
        int $clientsCount = 1000,
        int $eventsCount = 10000,
    ) {
        $this->outputCsvFile = $outputCsvFile;
        $this->clientsCount = $clientsCount;
        $this->eventsCount = $eventsCount;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('outputCsvFile', InputArgument::REQUIRED, 'Output CSV file');
        $this->addArgument('clientsCount', InputArgument::OPTIONAL, 'Count of clients (default: 1000)');
        $this->addArgument('eventsCount', InputArgument::OPTIONAL, 'Count of events (default: 10000)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fh = fopen($this->outputCsvFile, 'w');
        if (false === $fh) {
            throw new RuntimeException("Failed to write file '$this->outputCsvFile'");
        }

        foreach (ClientMessagesGenerator::clientsWithEventsGenerator($this->clientsCount, $this->eventsCount) as $value) {
            fputcsv($fh, [$value]);
        }

        fclose($fh);
        return Command::SUCCESS;
    }
}
