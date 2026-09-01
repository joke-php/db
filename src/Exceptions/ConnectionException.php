<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Exceptions;

/**
 * Исключение, возникающее при ошибках подключения к базе данных.
 *
 * Выбрасывается в случаях:
 * - Невозможность установить соединение (неверный хост, порт, учётные данные).
 * - Таймаут подключения.
 * - Потеря соединения во время выполнения запроса.
 * - Ошибки SSL/TLS-соединения.
 */
class ConnectionException extends DatabaseException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'Connection failed.',
            $code,
            $previous,
        );
    }
}
