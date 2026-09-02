<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Demo\Driver;

use Vasoft\Joke\Db\Contract\ConnectionInterface;

/**
 * Демонстрационная реализация ConnectionInterface на основе внутреннего массива.
 *
 * Не использует реальную БД.
 * Данные хранятся в памяти, транзакции эмулируются через копию состояния.
 *
 * «SQL» интерпретируется упрощённо: поддерживаются базовые операции SELECT, INSERT, CREATE TABLE.
 */
class MemoryConnection extends BaseConnection implements ConnectionInterface
{
    public function isConnected(): bool
    {
        // JsonConnection всегда «подключён» — данные в памяти
        return true;
    }

    public function disconnect(): void
    {
        // Нет внешнего ресурса для освобождения
    }
}
