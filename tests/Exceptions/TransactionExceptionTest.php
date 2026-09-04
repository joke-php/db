<?php

declare(strict_types=1);

namespace Vasoft\Joke\Db\Sql\Tests\Exceptions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Db\Sql\Exceptions\TransactionException;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Joke\Db\Sql\Exceptions\TransactionException
 */
#[TestDox('TransactionException - исключения связанные с транзакциями')]
#[CoversClass(TransactionException::class)]
final class TransactionExceptionTest extends TestCase
{
    public function testDefaultText(): void
    {
        $e = new TransactionException();
        self::assertSame('Transaction failed.', $e->getMessage());
    }
}
