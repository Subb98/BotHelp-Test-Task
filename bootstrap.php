<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Custom function for get value of environment variable
 *
 * @param string $envName
 * @return string
 */
function customGetEnv(string $envName): string
{
    $value = getenv($envName);
    if (false === $value) {
        throw new \InvalidArgumentException("Environment variable '$envName' does not exists!");
    }
    return $value;
}

$wsServerHost = customGetEnv('WS_SERVER_HOST');
$wsServerPort = (int)customGetEnv('WS_SERVER_PORT');
$wsServerUseSsl = (bool)customGetEnv('WS_SERVER_USE_SSL');
$wsServerSslCert = customGetEnv('WS_SERVER_SSL_CERT');
$wsServerSslKey = customGetEnv('WS_SERVER_SSL_KEY');
$wsServerBasicAuth = customGetEnv('WS_SERVER_BASIC_AUTH');

$config = [
    'ws_server_host' => $wsServerHost,
    'ws_server_port' => $wsServerPort,
    'ws_server_use_ssl' => $wsServerUseSsl,
    'ws_server_ssl_cert' => $wsServerSslCert,
    'ws_server_ssl_key' => $wsServerSslKey,
    'ws_server_basic_auth' => $wsServerBasicAuth,
];
