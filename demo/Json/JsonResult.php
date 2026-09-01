<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Demo\Json;

use Vasoft\Joke\Db\Contract\ResultInterface;

/**
 * Реализация ResultInterface для JSON-хранилища (демонстрационная).
 *
 * Данные полностью буферизуются в памяти при создании объекта.
 * Поддерживает многократную итерацию и чтение.
 */
class JsonResult implements ResultInterface
{
    /**
     * @param list<array<string,mixed>> $data записи результата
     */
    public function __construct(private readonly array $data) {}

    /**
     * @return \Traversable<int, array<string, mixed>>
     */
    public function getIterator(): \Traversable
    {
        while ($row = $this->data) {
            yield $row;
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @return null|array<string, mixed>
     */
    public function one(): ?array
    {
        return $this->data[0] ?? null;
    }

    public function count(): int
    {
        return count($this->data);
    }
}
