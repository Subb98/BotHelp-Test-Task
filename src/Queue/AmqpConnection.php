<?php

declare(strict_types=1);

namespace Subb98\BotHelpTestTask\Queue;

use DomainException;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Не успеваю проработать вопрос с возможными ошибками
 * при работе с одним коннектом, поэтому не является синглтоном по сути,
 * класс служит больше глобальным объектом с настройками подключения
 */
final class AmqpConnection
{
    private static string $host;
    private static int $port;
    private static string $user;
    private static string $password;
    private static string $vhost;

    public static function setConfig(
        string $host,
        int $port,
        string $user,
        string $password,
        string $vhost
    ): void {
        self::$host = $host;
        self::$port = $port;
        self::$user = $user;
        self::$password = $password;
        self::$vhost = $vhost;
    }

    public static function getInstance(): AbstractConnection
    {
        return new AMQPStreamConnection(
            self::$host,
            self::$port,
            self::$user,
            self::$password,
            self::$vhost
        );
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new DomainException("Cannot unserialize singleton");
    }
}
