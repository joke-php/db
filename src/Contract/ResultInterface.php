<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Contract;

/**
 * Представляет результат выполнения запроса к базе данных.
 *
 * Реализации могут быть буферизованными или небуферизированными
 * Небуферизованные результаты являются однопоточными: итерация или извлечение данных
 * расходует курсор, повторное чтение невозможно без повторного выполнения запроса.
 *
 * @extends \IteratorAggregate<int, array<string, mixed>>
 */
interface ResultInterface extends \IteratorAggregate, \Countable
{
    /**
     * Возвращает все строки результата в виде списка ассоциативных массивов.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array;

    /**
     * Возвращает первую строку результата в виде ассоциативного массива,
     * или null, если результат пуст.
     *
     * @return null|array<string, mixed>
     */
    public function one(): ?array;

    /**
     * Возвращает количество строк в результате.
     *
     * Примечание: для небуферизированными SELECT-запросов может выбрасывать исключение,
     * так как количество строк недоступно без полной выборки.
     */
    public function count(): int;
}
