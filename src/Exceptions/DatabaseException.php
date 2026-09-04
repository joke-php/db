<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Sql\Exceptions;

use Vasoft\Joke\Exceptions\JokeException;

/**
 * Базовое исключение для всех ошибок, связанных с базой данных.
 *
 * Все специфичные исключения модуля БД (ошибки подключения, выполнения запросов,
 * транзакций) наследуются от этого класса. Позволяет перехватывать все ошибки
 * базы данных единым catch-блоком:
 *
 * @see ConnectionException Ошибки подключения к БД.
 * @see QueryException Ошибки выполнения SQL-запросов.
 * @see TransactionException Ошибки управления транзакциями.
 */
class DatabaseException extends JokeException {}
