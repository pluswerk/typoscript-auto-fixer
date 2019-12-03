<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Identifier;

/**
 * Class IdentifierTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Identifier
 */
final class IdentifierTest extends TestCase
{
    /**
     * @test
     * @dataProvider identifierProvider
     */
    public function identifierCreatesArrayFromString($string, $expected): void
    {
        $identifier = new Identifier($string);

        foreach ($identifier as $item) {
            $this->assertEquals($expected[$identifier->key()], $item);
        }
        $this->assertEquals(count($expected), $identifier->count());
    }

    /**
     * @test
     * @dataProvider identifierProvider
     */
    public function reverseItemsIdentifierCanBeFetched($string, $expected): void
    {
        $identifier = new Identifier($string);
        $expected = array_reverse($expected);

        $i = 0;
        foreach ($identifier->reverseItems() as $item) {
            $this->assertEquals($expected[$i++], $item);
        }
    }

    public function identifierProvider()
    {
        return [
            [
                'string' => 'foo.bar',
                'expected' => [
                    0 => 'foo',
                    1 => 'bar'
                ]
            ],
            [
                'string' => 'foo',
                'expected' => [
                    0 => 'foo'
                ]
            ],
            [
                'string' => 'foo.nested.bar',
                'expected' => [
                    0 => 'foo',
                    1 => 'nested',
                    2 => 'bar'
                ]
            ]
        ];
    }
}
