<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Sql;

use Vasoft\Joke\Db\Exceptions\TransactionException;

/**
 * Режим эмуляции транзакций для драйверов без нативной поддержки.
 *
 * Определяет поведение методов {@see ConnectionInterface::beginTransaction()},
 * {@see ConnectionInterface::commit()} и {@see ConnectionInterface::rollBack()}
 * при отсутствии реальной поддержки транзакций в выбранном драйвере/СУБД.
 */
enum TransactionSupportPolicy: string
{
    /**
     * Выбрасывать исключение при попытке использования транзакций.
     *
     * Если драйвер не поддерживает транзакции, вызов beginTransaction()/commit()/rollBack()
     * приведёт к выбросу {@see TransactionException}.
     *
     * Подходит для СУБД с полной поддержкой транзакций, где важно явно контролировать
     * корректность их использования.
     */
    case THROW = 'throw';
    /**
     * Игнорировать вызовы методов транзакций.
     *
     * Методы beginTransaction(), commit() и rollBack() становятся no-op операциями:
     * они выполняются успешно, но не оказывают реального влияния на данные.
     *
     * Позволяет использовать единый код с транзакциями для разных драйверов,
     * включая те, где транзакции не реализованы или эмулируются.
     */
    case SILENCE = 'silence';
}
