<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Tests\Sql;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Config\Exceptions\ConfigException;
use Vasoft\Joke\Db\Sql\SqlConnectionConfig;
use Vasoft\Joke\Db\Sql\TransactionSupportPolicy;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Joke\Db\Sql\SqlConnectionConfig
 */
#[CoversClass(SqlConnectionConfig::class)]
#[TestDox('SqlConnectionConfig - конфигурация SQL подключений')]
final class SqlConnectionConfigTest extends TestCase
{
    #[DataProvider('provideFrozenCases')]
    #[TestDox('Нельзя менять настройки после заморозки')]
    public function testFrozen(string $setter, mixed $value): void
    {
        $config = new SqlConnectionConfig('test', 'example');
        $config->freeze();
        self::expectException(ConfigException::class);
        $config->{$setter}($value);
    }

    public static function provideFrozenCases(): iterable
    {
        yield ['setCharset', 'win1251'];
        yield ['setTransactionSupportPolicy', TransactionSupportPolicy::THROW];
    }

    #[DataProvider('provideSetAndGetCases')]
    #[TestDox('До заморозки возможно менять настройки')]
    public function testSetAndGet(string $setName, string $getName, mixed $value): void
    {
        $config = new SqlConnectionConfig('test', 'example');
        $config->{$setName}($value);
        self::assertSame($value, $config->{$getName});
    }

    public static function provideSetAndGetCases(): iterable
    {
        yield ['setCharset', 'charset', 'win1251'];
        yield ['setTransactionSupportPolicy', 'transactionSupportPolicy', TransactionSupportPolicy::SILENCE];
    }

    #[TestDox('Возвращает специфические опции по имени или null')]
    public function testGetSpecificOptions(): void
    {
        $options = [
            'intOption' => 12,
            'floatOption' => 12.1,
            'boolOption' => false,
            'stringOption' => 'false',
        ];
        $config = new SqlConnectionConfig('test', 'example', specificOptions: $options);
        self::assertSame($options['intOption'], $config->getSpecificOption('intOption'));
        self::assertSame($options['floatOption'], $config->getSpecificOption('floatOption'));
        self::assertSame($options['boolOption'], $config->getSpecificOption('boolOption'));
        self::assertSame($options['stringOption'], $config->getSpecificOption('stringOption'));
        self::assertNull($config->getSpecificOption('unknown'));
    }
}
