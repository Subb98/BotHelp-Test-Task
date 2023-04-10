<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue;

use Symfony\Component\Process\Process;

class Supervisor
{
    /** @var int Интервал проверки процессов */
    public int $interval = 5;

    /** @var int Время, отведённое на завершение подпроцессов */
    public int $stopTimeout = 10;

    public int $stopSignal = 3;

    public int $reloadSignal = 1;

    public string $outputLogFile = '';

    /** @var Process[] Массив запущенных подпроцессов */
    private array $processes;

    private bool $stop = false;

    private bool $reload = false;

    public function startSupervisor(): int
    {
        $this->registerSignals();

        while (false === $this->stop) {
            $this->check();

            sleep($this->interval);

            pcntl_signal_dispatch();

            if (true === $this->reload) {
                $this->stopProcesses();
                $this->reload = false;
            }
        }

        $this->stopProcesses();
        $this->log("Stopped.");

        return 0;
    }

    public function registerSignals(): void
    {
        pcntl_signal(SIGINT, [$this, 'handleShutdown']);
        pcntl_signal($this->stopSignal, [$this, 'handleShutdown']);
        pcntl_signal($this->reloadSignal, [$this, 'handleReload']);
    }

    public function handleShutdown(): void
    {
        $this->stop = true;
        $this->log("Shutting down...");
    }

    public function handleReload(): void
    {
        $this->reload = true;
        $this->log("Reloading...");
    }

    public function check(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            $queueName = 'ch' . $i;
            $command = ['/usr/local/bin/php', '/usr/local/src/bin/console', 'app:start-consumer', $queueName];

            $process = $this->processes[$queueName] ?? null;

            if (!$process instanceof Process || !$process->isRunning()) {
                $this->log("Running {$queueName} process...");
                $process = $this->startProcess($command);
                $this->processes[$queueName] = $process;
            } else {
                if (!empty($process->getOutput())) {
                    $this->log($process->getOutput());
                    $process->clearOutput();
                }
                if (!empty($process->getErrorOutput())) {
                    $this->log($process->getOutput());
                    $process->clearErrorOutput();
                }
            }
        }
    }

    public function startProcess(array $command): Process
    {
        $process = new Process($command);
        $process->setTimeout(null);
        $process->start();
        return $process;
    }

    public function stopProcesses(): void
    {
        foreach ($this->processes as $id => $process) {
            if ($process->isRunning()) {
                $this->log("Stopping {$id} process...");
                $process->stop($this->stopTimeout, SIGINT);
            } else {
                $this->log("Process {$id} is not running.");
            }
        }
    }

    public function log(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
