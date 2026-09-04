<?php


declare(strict_types=1);

namespace Vasoft\Joke\Db\Sql\Tests\Exceptions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Db\Sql\Exceptions\QueryException;

/**
 * @internal
 *
 * @coversDefaultClass \Vasoft\Joke\Db\Sql\Exceptions\QueryException
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
