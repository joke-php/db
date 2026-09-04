<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Sql;

use Vasoft\Joke\Config\AbstractConfig;
use Vasoft\Joke\Config\Exceptions\ConfigException;

/**
 * Конфигурация SQL-подключения к базе данных.
 *
 * Хранит параметры подключения (driver, host, port, credentials)
 * и настройки поведения (кодировка, политика транзакций).
 */
class SqlConnectionConfig extends AbstractConfig
{
    /**
     * Политика поддержки транзакций драйвером.
     *
     * Определяет поведение при вызове методов транзакций, если драйвер
     * не поддерживает их нативно:
     * - {@see TransactionSupportPolicy::THROW} — выбрасывать исключение
     * - {@see TransactionSupportPolicy::SILENCE} — игнорировать вызовы (no-op)
     */
    public private(set) TransactionSupportPolicy $transactionSupportPolicy = TransactionSupportPolicy::THROW;
    /**
     * Кодировка соединения с базой данных.
     *
     * Используется при формировании DSN и установке кодировки после подключения.
     * По умолчанию используется utf8mb4 для полной поддержки Unicode.
     *
     * @var non-empty-string
     */
    public private(set) string $charset = 'utf8mb4';

    /**
     * @param non-empty-string                         $driver          Название драйвера БД (mysql, pgsql, sqlite и т.д.)
     * @param non-empty-string                         $database        Имя базы данных или путь к файлу (для SQLite)
     * @param string                                   $host            Хост сервера БД (пустая строка для локальных/Unix-сокетов)
     * @param null|int                                 $port            Порт сервера БД (null для порта по умолчанию)
     * @param null|string                              $username        Имя пользователя для аутентификации
     * @param null|string                              $password        Пароль для аутентификации
     * @param array<string,null|bool|float|int|string> $specificOptions Специфичные опции драйвера (например, параметры для DSN-строки)
     */
    public function __construct(
        public readonly string $driver,
        public readonly string $database,
        public readonly string $host = '',
        public readonly ?int $port = null,
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly array $specificOptions = [],
    ) {}

    /**
     * Возвращает специфичную опцию драйвера по имени.
     *
     * @param string $name Имя опции
     *
     * @return null|bool|float|int|string Значение опции или null, если опция не задана
     */
    public function getSpecificOption(string $name): int|float|bool|string|null
    {
        return $this->specificOptions[$name] ?? null;
    }

    /**
     * Устанавливает кодировку соединения.
     *
     * @param non-empty-string $charset Кодировка (например, 'utf8mb4', 'latin1')
     *
     * @throws ConfigException Если конфигурация уже заморожена
     */
    public function setCharset(string $charset): static
    {
        $this->guard();
        $this->charset = $charset;

        return $this;
    }

    /**
     * Устанавливает политику поддержки транзакций.
     *
     * @throws ConfigException Если конфигурация уже заморожена
     */
    public function setTransactionSupportPolicy(TransactionSupportPolicy $transactionSupportPolicy): static
    {
        $this->guard();
        $this->transactionSupportPolicy = $transactionSupportPolicy;

        return $this;
    }
}
