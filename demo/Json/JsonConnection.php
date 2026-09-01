<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Demo\Json;

use Vasoft\Joke\Db\Contract\ConnectionInterface;
use Vasoft\Joke\Db\Contract\ResultInterface;
use Vasoft\Joke\Db\Exceptions\QueryException;
use Vasoft\Joke\Db\Exceptions\TransactionException;

/**
 * Демонстрационная реализация ConnectionInterface на основе JSON-файла.
 *
 * Не использует реальную БД.
 * Данные хранятся в памяти, транзакции эмулируются через копию состояния.
 *
 * «SQL» интерпретируется упрощённо: поддерживается только формат
 * "SELECT * FROM <имя_коллекции>" для демонстрации работы ResultInterface.
 */
class JsonConnection implements ConnectionInterface
{
    /**
     * @var array<string, list<array<string, mixed>>> хранилище данных по коллекциям
     */
    private array $store = [];

    /**
     * @var array<string, null|list<array<string, mixed>>> Снимок состояния для транзакций.
     *                                                     null означает отсутствие активной транзакции.
     */
    private ?array $transactionSnapshot = null;

    /**
     * @param array<string, list<array<string, mixed>>> $initialData Начальные данные.
     *                                                               Ключ — имя коллекции, значение — список записей.
     */
    public function __construct(array $initialData = [])
    {
        $this->store = $initialData;
    }

    public function query(string $sql, array $params = []): ResultInterface
    {
        // Упрощённый парсинг: SELECT * FROM collection_name
        if (!preg_match('/^\s*SELECT\s+\*\s+FROM\s+(\w+)\s*$/i', trim($sql), $matches)) {
            throw new QueryException(
                "JsonConnection поддерживает только запросы вида 'SELECT * FROM <collection>'. Получен: {$sql}",
            );
        }

        $collection = strtolower($matches[1]);
        $data = $this->store[$collection] ?? [];

        return new JsonResult($data);
    }

    /**
     * Для демо: execute всегда успешен, но ничего не меняет.
     * В реальной реализации здесь был бы парсинг INSERT/UPDATE/DELETE.
     */
    public function execute(string $sql, array $params = []): int
    {
        return 0;
    }

    public function beginTransaction(): void
    {
        if (null !== $this->transactionSnapshot) {
            throw new TransactionException('Вложенные транзакции не поддерживаются в JsonConnection.');
        }

        $this->transactionSnapshot = $this->store;
    }

    public function commit(): void
    {
        if (null === $this->transactionSnapshot) {
            throw new TransactionException('Нет активной транзакции.');
        }

        $this->transactionSnapshot = null;
    }

    public function rollBack(): void
    {
        if (null === $this->transactionSnapshot) {
            throw new TransactionException('Нет активной транзакции.');
        }

        $this->store = $this->transactionSnapshot;
        $this->transactionSnapshot = null;
    }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();

            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

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
