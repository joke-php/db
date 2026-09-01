<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Exceptions;

/**
 * Исключение, возникающее при ошибках управления транзакциями.
 *
 * Выбрасывается в случаях:
 * - Попытка commit/rollback без активной транзакции.
 * - Ошибки создания, освобождения или отката savepoint'ов.
 * - Транзакции не поддерживаются драйвером.
 * - Конфликты при вложенных транзакциях.
 */
class TransactionException extends DatabaseException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'Transaction failed.',
            $code,
            $previous,
        );
    }
}
