<?php

declare(strict_types=1);

namespace Camelot\DoctrineQueryFormatter\Tests;

use Camelot\DoctrineQueryFormatter\QuerySqlFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @copyright Copyright (c) 2011 Fabien Potencier, Doctrine Project
 *
 * @covers \Camelot\DoctrineQueryFormatter\QuerySqlFormatter
 */
final class QuerySqlFormatterTest extends TestCase
{
    public function testReplaceQueryParametersWithPostgresCasting(): void
    {
        $extension = new QuerySqlFormatter();
        $query = 'a=? OR (1)::string OR b=?';
        $parameters = [1, 2];

        $result = $extension->replaceQueryParameters($query, $parameters);
        static::assertEquals('a=1 OR (1)::string OR b=2', $result);
    }

    public function testReplaceQueryParametersWithStartingIndexAtOne(): void
    {
        $extension = new QuerySqlFormatter();
        $query = 'a=? OR b=?';
        $parameters = [
            1 => 1,
            2 => 2,
        ];

        $result = $extension->replaceQueryParameters($query, $parameters);
        static::assertEquals('a=1 OR b=2', $result);
    }

    public function testReplaceQueryParameters(): void
    {
        $extension = new QuerySqlFormatter();
        $query = 'a=? OR b=?';
        $parameters = [
            1,
            2,
        ];

        $result = $extension->replaceQueryParameters($query, $parameters);
        static::assertEquals('a=1 OR b=2', $result);
    }

    public function testReplaceQueryParametersWithNamedIndex(): void
    {
        $extension = new QuerySqlFormatter();
        $query = 'a=:a OR b=:b';
        $parameters = [
            'a' => 1,
            'b' => 2,
        ];

        $result = $extension->replaceQueryParameters($query, $parameters);
        static::assertEquals('a=1 OR b=2', $result);
    }

    public function testEscapeBinaryParameter(): void
    {
        $binaryString = pack('H*', '9d40b8c1417f42d099af4782ec4b20b6');
        static::assertEquals('0x9D40B8C1417F42D099AF4782EC4B20B6', static::escapeFunction($binaryString));
    }

    public function testEscapeStringParameter(): void
    {
        static::assertEquals("'test string'", static::escapeFunction('test string'));
    }

    public function testEscapeArrayParameter(): void
    {
        static::assertEquals("1, NULL, 'test', foo", static::escapeFunction([1, null, 'test', $this->getDummyClass('foo')]));
    }

    public function testEscapeObjectParameter(): void
    {
        $object = $this->getDummyClass('bar');
        static::assertEquals('bar', static::escapeFunction($object));
    }

    public function testEscapeNullParameter(): void
    {
        static::assertEquals('NULL', static::escapeFunction(null));
    }

    public function testEscapeBooleanParameter(): void
    {
        static::assertEquals('1', static::escapeFunction(true));
    }

    private static function escapeFunction($val)
    {
        $rc = new \ReflectionClass(QuerySqlFormatter::class);
        $rm = $rc->getMethod('escapeFunction');
        $rm->setAccessible(true);

        return $rm->invoke(new QuerySqlFormatter(), $val);
    }

    private function getDummyClass(string $str): object
    {
        return new class ($str) {
            protected string $str;

            public function __construct(string $str)
            {
                $this->str = $str;
            }

            public function __toString()
            {
                return $this->str;
            }
        };
    }
}
