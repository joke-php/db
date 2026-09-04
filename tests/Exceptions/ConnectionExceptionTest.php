<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Tests\Exceptions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Db\Exceptions\ConnectionException;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Joke\Db\Exceptions\ConnectionException
 */
#[TestDox('ConnectionException - исключения связанные с соединениями')]
#[CoversClass(ConnectionException::class)]
final class ConnectionExceptionTest extends TestCase
{
    public function testDefaultText(): void
    {
        $e = new ConnectionException();
        self::assertSame('Connection failed.', $e->getMessage());
    }
}
