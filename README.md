# BotHelp Test Task
## Описание
Есть веб-api, непрерывно принимающее события (ограничимся 10000 событий) для группы аккаунтов (1000 аккаунтов)
и складывающее их в очередь. Каждое событие связано с определённым аккаунтом и важно, чтобы события аккаунта
обрабатывались в том же порядке, в котором поступили в очередь. Обработка события занимает 1 секунду
(эмулировать с помощью sleep). Сделать обработку очереди событий максимально быстрой на данной конкретной машине.
Код писать на PHP. Можно использовать фреймворки и инструменты такие как RabbitMQ, Redis, MySQL и т.д.

## Примечание
В качестве Websocket-клиента для тестирования вручную можно использовать [websocat](https://github.com/vi/websocat).
Для подключения к серверу с SSL и самоподписным сертификатом необходимо использовать опцию `--insecure`, пример:

```shell
websocat wss://0.0.0.0:8000 --insecure
```

Пример генерирования нового самоподписанного сертификата:
```shell
openssl req -newkey rsa:4096 \
            -x509 \
            -sha256 \
            -days 3650 \
            -nodes \
            -out localhost.crt \
            -keyout localhost.key \
            -subj "/C=RU/ST=Perm Krai/L=Perm/O=OpenSSL/OU=0.0.0.0/CN=localhost"
```