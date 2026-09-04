<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Exceptions;

/**
 * Исключение, возникающее при ошибках выполнения SQL-запросов.
 *
 * Выбрасывается в случаях:
 * - Синтаксические ошибки в SQL.
 * - Нарушения ограничений (unique, foreign key, not null).
 * - Deadlock'и и таймауты блокировок.
 * - Несуществующие таблицы или столбцы.
 * - Ошибки подготовленных выражений (неверные параметры, типы).
 */
class QueryException extends DatabaseException
{
    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'Query failed.',
            $code,
            $previous,
        );
    }
}
