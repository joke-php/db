<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Sql\Tests\Driver\Pdo;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Config\Exceptions\ConfigException;
use Vasoft\Joke\Db\Sql\Driver\Pdo\Driver;
use Vasoft\Joke\Db\Sql\SqlConnectionConfig;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Joke\Db\Sql\Driver\Pdo\Driver
 */
#[CoversClass(Driver::class)]
#[TestDox('Driver - генератор DSN строк для PDO')]
final class DriverTest extends TestCase
{
    #[DataProvider('provideDsnGenerationCases')]
    #[TestDox('Генерирует корректный DSN для {0}')]
    public function testDsnGeneration(string $driverName, array $configArgs, string $charset, string $expectedDsn): void
    {
        $config = new SqlConnectionConfig(...$configArgs);
        if ('' !== $charset) {
            $config->setCharset($charset);
        }
        $driver = Driver::from($driverName);

        self::assertSame($expectedDsn, $driver->dsn($config));
    }

    public static function provideDsnGenerationCases(): iterable
    {
        yield 'Firebird' => [
            'firebird',
            [
                'driver' => 'firebird',
                'database' => 'example',
                'host' => 'localhost',
                'port' => 3306,
                'specificOptions' => ['role' => 'admin', 'dialect' => 1],
            ],
            '',
            'firebird:dbname=localhost/3306:example;charset=utf8mb4;role=admin;dialect=1',
        ];
        yield 'Firebird local path' => [
            'firebird',
            ['driver' => 'firebird', 'database' => '/opt/firebird/data/test.fdb', 'host' => ''],
            '',
            'firebird:dbname=/opt/firebird/data/test.fdb;charset=utf8mb4',
        ];
        yield 'IBM' => [
            'ibm',
            [
                'driver' => 'ibm',
                'database' => 'example',
                'host' => 'localhost',
                'port' => 3306,
            ],
            '',
            'ibm:hostname=localhost;port=3306;database=example',
        ];
        yield 'Informix' => [
            'informix',
            [
                'driver' => 'informix',
                'database' => '',
                'specificOptions' => ['DSN' => 'MyLegacyDB'],
            ],
            '',
            'informix:DSN=MyLegacyDB',
        ];
        yield 'OCI' => [
            'oci',
            [
                'driver' => 'oci',
                'database' => '//localhost:1521/mydb',
            ],
            '',
            'oci:dbname=//localhost:1521/mydb;charset=utf8mb4',
        ];
        yield 'OCI via TNS' => [
            'oci',
            [
                'driver' => 'oci',
                'database' => '',
                'specificOptions' => ['tns' => 'MY_TNS_ALIAS'],
            ],
            '',
            'oci:charset=utf8mb4;tns=MY_TNS_ALIAS',
        ];
        yield 'Cubrid' => [
            'cubrid',
            [
                'driver' => 'cubrid',
                'database' => 'denodb',
                'host' => 'localhost',
                'port' => 33000,
            ],
            '',
            'cubrid:host=localhost;port=33000;dbname=denodb',
        ];
        yield 'ODBC via DSN name' => [
            'odbc',
            ['driver' => 'odbc', 'database' => 'ignored', 'specificOptions' => ['DSN' => 'MyLegacyDB']],
            '',
            'odbc:MyLegacyDB',
        ];
        yield 'ODBC via database' => [
            'odbc',
            ['driver' => 'odbc', 'database' => 'ignored'],
            '',
            'odbc:ignored',
        ];
        yield 'ODBC via Driver options' => [
            'odbc',
            [
                'driver' => 'odbc',
                'database' => '',
                'specificOptions' => [
                    'Driver' => '{SQL Server}',
                    'Server' => 'localhost',
                    'Database' => 'test',
                ],
            ],
            '',
            'odbc:Driver={SQL Server};Server=localhost;Database=test',
        ];
        yield 'MySQL basic' => [
            'mysql',
            ['driver' => 'mysql', 'database' => 'test_db', 'host' => 'localhost'],
            '',
            'mysql:host=localhost;dbname=test_db',
        ];

        yield 'MySQL with port and charset' => [
            'mysql',
            [
                'driver' => 'mysql',
                'database' => 'test_db',
                'host' => '127.0.0.1',
                'port' => 3307,
                'specificOptions' => ['charset' => 'utf8'],
            ],
            '',
            'mysql:host=127.0.0.1;port=3307;dbname=test_db;charset=utf8',
        ];

        yield 'SQLite file' => [
            'sqlite',
            ['driver' => 'sqlite', 'database' => '/var/db/app.sqlite'],
            '',
            'sqlite:/var/db/app.sqlite',
        ];

        yield 'PostgreSQL' => [
            'pgsql',
            [
                'driver' => 'pgsql',
                'database' => 'myapp',
                'host' => 'db.example.com',
                'port' => 5432,
                'specificOptions' => ['sslmode' => 'require'],
            ],
            '',
            'pgsql:host=db.example.com;port=5432;dbname=myapp;sslmode=require',
        ];

        yield 'SQLSRV (Microsoft)' => [
            'sqlsrv',
            [
                'driver' => 'sqlsrv',
                'database' => 'AdventureWorks',
                'host' => 'MSSQLSERVER',
                'username' => 'sa',
                'password' => 'secret',
            ],
            'UTF-8',
            'sqlsrv:Server=MSSQLSERVER;Database=AdventureWorks;UID=sa;PWD=secret;CharacterSet=UTF-8',
        ];

        yield 'DBLIB family (using mssql prefix)' => [
            'mssql',
            ['driver' => 'mssql', 'database' => 'test', 'host' => 'remote_host'],
            '',
            'mssql:host=remote_host;dbname=test;charset=utf8mb4',
        ];

        // Новый кейс: экранирование спецсимволов в значениях
        yield 'Escapes special chars in values' => [
            'mysql',
            [
                'driver' => 'mysql',
                'database' => 'db;name=x',
                'host' => 'localhost',
            ],
            '',
            'mysql:host=localhost;dbname=db\;name\=x',
        ];
    }

    #[TestDox('Выбрасывает исключение если отсутствует обязательный хост')]
    public function testMissingHostThrowsException(): void
    {
        $config = new SqlConnectionConfig(driver: 'pgsql', database: 'db');

        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs(
            "Property 'host' cannot be empty for '" . Driver::PGSQL->value . "'.",
        );

        Driver::PGSQL->dsn($config);
    }

    #[TestDox('Выбрасывает исключение если отсутствует имя базы данных')]
    public function testMissingDatabaseThrowsException(): void
    {
        $config = new SqlConnectionConfig('mysql', '', host: 'localhost');

        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs(
            "Property 'database' cannot be empty for '" . Driver::MYSQL->value . "'.",
        );

        Driver::MYSQL->dsn($config);
    }

    #[TestDox('Выбрасывает исключение если у OCI нет ни dbname, ни tns')]
    public function testOciRequiresDbnameOrTns(): void
    {
        $config = new SqlConnectionConfig(driver: 'oci', database: '');

        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs("DSN for 'oci' requires at least one of: dbname,tns.");

        Driver::OCI->dsn($config);
    }

    #[TestDox('Корректно обрабатывает альтернативные параметры (Unix Socket)')]
    public function testAlternativeParamsSuccess(): void
    {
        $config = new SqlConnectionConfig(
            driver: 'mysql',
            database: 'test',
            specificOptions: ['unix_socket' => '/var/run/mysqld/mysqld.sock'],
        );

        $dsn = Driver::MYSQL->dsn($config);
        self::assertStringContainsString('unix_socket=/var/run/mysqld/mysqld.sock', $dsn);
    }

    #[TestDox('Игнорирует null и пустые значения в опциях')]
    public function testSkipsEmptyOptions(): void
    {
        $config = new SqlConnectionConfig(
            driver: 'mysql',
            database: 'test',
            host: 'localhost',
            port: null,
            specificOptions: ['charset' => '', 'sslmode' => null],
        );

        $dsn = Driver::MYSQL->dsn($config);
        self::assertSame('mysql:host=localhost;dbname=test', $dsn);
    }

    #[TestDox('Поддерживает порт в формате Server,port для SQLSRV')]
    public function testSqlsrvPortFormat(): void
    {
        $config = new SqlConnectionConfig(
            driver: 'sqlsrv',
            database: 'db',
            host: '192.168.1.50',
            port: 1433,
        );

        $dsn = Driver::SQLSRV->dsn($config);
        self::assertStringContainsString('Server=192.168.1.50,1433', $dsn);
    }

    #[TestDox('Выбрасывает исключение если у ODBC нет ни `DSN`, ни `database`')]
    public function testOdbcRequires(): void
    {
        $config = new SqlConnectionConfig(driver: 'odbc', database: '');

        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs("DSN for 'odbc' requires at least one of: database, DSN.");

        Driver::ODBC->dsn($config);
    }

    #[TestDox('buildDsn Выбрасывает исключение если запрошен неизвестный драйвер')]
    public function testBuildDsnUnknownDriver(): void
    {
        $config = new SqlConnectionConfig(driver: 'unknown', database: '');

        self::expectException(ConfigException::class);
        self::expectExceptionMessageIs("Unknown database driver: 'unknown'.");

        Driver::buildDsn($config);
    }

    #[TestDox('buildDsn возвращает строку на основе конфига')]
    public function testBuildDsn(): void
    {
        $config = new SqlConnectionConfig(
            driver: 'sqlsrv',
            database: 'db',
            host: '192.168.1.50',
            port: 1433,
        );

        $dsn = Driver::buildDsn($config);
        self::assertStringContainsString('Server=192.168.1.50,1433', $dsn);
    }
}
