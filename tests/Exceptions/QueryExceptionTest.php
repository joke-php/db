<?php


declare(strict_types=1);

namespace Vasoft\Joke\Db\Tests\Exceptions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Db\Exceptions\QueryException;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Joke\Db\Exceptions\QueryException
 */
#[TestDox('QueryException - исключения связанные с запросами')]
#[CoversClass(QueryException::class)]
final class QueryExceptionTest extends TestCase
{
    public function testDefaultText(): void
    {
        $e = new QueryException();
        self::assertSame('Query failed.', $e->getMessage());
    }
}
