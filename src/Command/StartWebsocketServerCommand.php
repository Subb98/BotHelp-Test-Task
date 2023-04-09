<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Command;

use Subb98\BotHelpTestTask\Websocket\WebsocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartWebsocketServerCommand extends Command
{
    protected static $defaultName = 'app:start-websocket-server';
    protected static $defaultDescription = 'Starts the Websocket server.';

    private readonly string $host;
    private readonly int $port;
    private readonly bool $useSsl;
    private readonly string $sslCert;
    private readonly string $sslKey;

    public function __construct(
        string $host,
        int $port,
        bool $useSsl,
        string $sslCert,
        string $sslKey,
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->useSsl = $useSsl;
        $this->sslCert = $sslCert;
        $this->sslKey = $sslKey;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('optcmd', InputArgument::OPTIONAL, 'Command for WebsocketClient');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $websocketServer = new WebsocketServer($this->host, $this->port, $this->useSsl, $this->sslCert, $this->sslKey);
        $websocketServer->startWebsocketServer();

        return Command::SUCCESS;
    }
}
