<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Config\Exceptions\ConfigException;
use Vasoft\Joke\Db\DatabaseConfig;
use Vasoft\Joke\Db\Demo\Json\JsonConnection;

/**
 * @coversDefaultClass \Vasoft\Joke\Db\DatabaseConfig
 *
 * @internal
 */
#[CoversClass(DatabaseConfig::class)]
#[TestDox('DatabaseConfig - конфигурация модуля')]
final class DatabaseConfigTest extends TestCase
{
    #[TestDox('Принимает различные типы описывающие подключение')]
    public function testConnectionVariants(): void
    {
        $config = new DatabaseConfig()
            ->addConnection('example1', JsonConnection::class)
            ->addConnection('example2', new JsonConnection())
            ->addConnection('example3', static fn() => new JsonConnection());

        self::assertSame(
            JsonConnection::class,
            $config->getConnection('example1'),
            'Конфигурация не приняла имя класса',
        );
        self::assertInstanceOf(
            JsonConnection::class,
            $config->getConnection('example2'),
            'Конфигурация не приняла экземпляр класса',
        );
        self::assertIsCallable($config->getConnection('example3'), 'Конфигурация не фабрику');
    }

    #[TestDox('При заморозке устанавливает имя соединения по умолчанию')]
    public function testDefaultConnectionName(): void
    {
        $config = new DatabaseConfig()
            ->addConnection('example10', JsonConnection::class)
            ->addConnection('example2', new JsonConnection())
            ->addConnection('example31', static fn() => new JsonConnection());
        self::assertSame('', $config->defaultConnectionName, 'Имя по умолчанию изначально не задано');
        $config->freeze();
        self::assertSame(
            'example10',
            $config->defaultConnectionName,
            'Имя по умолчанию должно быть равным первому зарегистрированному.',
        );
    }

    #[TestDox('Установленное имя соединения по умолчанию не изменяется')]
    public function testSetDefaultName(): void
    {
        $config = new DatabaseConfig()
            ->addConnection('example10', JsonConnection::class)
            ->addConnection('example2', new JsonConnection())
            ->addConnection('example31', static fn() => new JsonConnection());
        $config->setDefaultConnectionName('example31');
        $config->freeze();
        self::assertSame('example31', $config->defaultConnectionName);
    }

    #[TestDox('При заморозке должно быть зарегистрировано хотя бы одно соединение')]
    public function testEmptyConnections(): void
    {
        $config = new DatabaseConfig();
        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs(
            'At least one database connection must be registered before freezing the configuration.',
        );
        $config->freeze();
    }

    #[TestDox('После заморозки нельзя менять имя соединения по умолчанию')]
    public function testSetDefaultNameFrozen(): void
    {
        $config = new DatabaseConfig()->addConnection('example10', JsonConnection::class);
        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs(
            'Cannot modify frozen configuration of [Vasoft\Joke\Db\DatabaseConfig].',
        );
        $config->freeze();
        $config->setDefaultConnectionName('example31');
    }

    #[TestDox('После заморозки добавлять соединения возможно')]
    public function testAddConnectionAfterFreeze(): void
    {
        $config = new DatabaseConfig()->addConnection('example1', JsonConnection::class);
        $config->freeze();
        $config->addConnection('example2', new JsonConnection());

        self::assertInstanceOf(JsonConnection::class, $config->getConnection('example2'));
    }

    #[TestDox('Повторная регистрация с тем же именем перезаписывает')]
    public function testRewriteConnection(): void
    {
        $entity = new JsonConnection();
        $config = new DatabaseConfig()
            ->addConnection('example1', JsonConnection::class)
            ->setDefaultConnectionName('example1')
            ->addConnection('example1', $entity);

        self::assertSame($entity, $config->getConnection('example1'));
    }

    #[TestDox('Повторная регистрация соединения по умолчанию после заморозки не возможна')]
    public function testRewriteConnectionAfterFreeze(): void
    {
        $config = new DatabaseConfig()
            ->addConnection('example1', JsonConnection::class)
            ->freeze();
        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs(
            "Cannot replace the default connection 'example1' after configuration is frozen.",
        );
        $config->addConnection('example1', new JsonConnection());
    }

    #[TestDox('Соединение необходимо зарегистрировать прежде чем назначать соединением по умолчанию.')]
    public function testSetAsDefaultUnknownConnection(): void
    {
        $config = new DatabaseConfig();
        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs("Connection 'unknown' is not registered. Cannot set as default.");
        $config->setDefaultConnectionName('unknown');
    }

    #[TestDox('Исключение если запрошено не существующее соединение.')]
    public function testGetUnknownConnection(): void
    {
        $config = new DatabaseConfig();
        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs("Connection 'unknown' is not registered.");
        $config->getConnection('unknown');
    }

    #[TestDox('Исключение если запрошено не существующее соединение по умолчанию.')]
    public function testGetUnknownDefaultConnection(): void
    {
        $config = new DatabaseConfig();
        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs(
            'Default connection is not configured. Register at least one connection or set default explicitly.',
        );
        $config->getConnection();
    }

    #[TestDox('Возвращает соединение по умолчанию если не указано имя')]
    public function testGetDefaultConnection(): void
    {
        $entity = new JsonConnection();
        $config = new DatabaseConfig()
            ->addConnection('example1', JsonConnection::class)
            ->addConnection('example2', $entity)
            ->setDefaultConnectionName('example2');

        self::assertSame($entity, $config->getConnection());
    }
}
