<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

/** @var array $config */

use Symfony\Component\Process\Process;

class SupervisorController
{
    /**
     * Интервал проверки процессов
     * @var int
     */
    public $interval = 5;

    /**
     * Время, отведённое на завершение подпроцессов
     * @var int
     */
    public $stopTimeout = 10;

    public $stopSignal = 3;

    public $reloadSignal = 1;

    /**
     * Массив запущенных подпроцессов
     * @var Process[]
     */
    private $_processes;

    private $stop = false;

    private $reload = false;

    public function actionRun()
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            $this->log("This controller couldn't be used on Windows");
            return 1;
        }
        if (!function_exists('pcntl_signal')) {
            $this->log("PCNTL extension is required");
            return 1;
        }

        $this->registerSignals();
        while ($this->stop === false) {
            $this->check();
            sleep($this->interval);
            pcntl_signal_dispatch();
            if ($this->reload === true) {
                $this->stopProcesses();
                $this->reload = false;
            }
        }
        $this->stopProcesses();
        $this->log("Stopped.");
        return 0;
    }

    public function registerSignals()
    {
        pcntl_signal(SIGINT, [$this, 'handleShutdown']);
        pcntl_signal($this->stopSignal, [$this, 'handleShutdown']);
        pcntl_signal($this->reloadSignal, [$this, 'handleReload']);
    }

    public function handleShutdown()
    {
        $this->stop = true;
        $this->log("Shutting down...");
    }

    public function handleReload()
    {
        $this->reload = true;
        $this->log("Reloading...");
    }

    public function check()
    {
        for ($i = 1; $i <= 100; $i++) {
            $queueName = 'ch' . $i;
            $command = ['/usr/local/bin/php', '/usr/local/src/public/consumer.php', $queueName];
            if (!isset($this->_processes[$queueName])) {
                $process = null;
            } else {
                $process = $this->_processes[$queueName];
            }
            if (!$process instanceof Process || !$process->isRunning()) {
                $this->log("Running {$queueName} process...");
                $process = $this->startProcess($command);
                $this->_processes[$queueName] = $process;
            } else {
                if (!empty($process->getOutput())) {
                    var_dump($process->getOutput());
                    $process->clearOutput();
                }
                if (!empty($process->getErrorOutput())) {
                    var_dump($process->getOutput());
                    $process->clearErrorOutput();
                }
            }
        }
    }

    public function startProcess($command)
    {
        $process = new Process($command);
        $process->setTimeout(null);
        $process->start();
        return $process;
    }

    public function stopProcesses()
    {
        foreach ($this->_processes as $id => $process) {
            if ($process->isRunning()) {
                $this->log("Stopping {$id} process...");
                $process->stop($this->stopTimeout, SIGINT);
            } else {
                $this->log("Process {$id} is not running.");
            }
        }
    }

    public function log(string $string): void
    {
        echo "$string\n";
    }
}

$c = new SupervisorController();
$c->actionRun();
