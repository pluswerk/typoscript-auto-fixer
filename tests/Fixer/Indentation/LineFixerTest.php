<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\Indentation;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\LineFixer;

/**
 * Class LineFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\Indentation
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\LineFixer
 */
final class LineFixerTest extends TestCase
{
    /**
     * @var LineFixer
     */
    private $lineFixer;

    protected function setUp(): void
    {
        $this->lineFixer = new LineFixer();
    }

    /**
     * @test
     * @dataProvider lineProvider
     */
    public function indentationIsCorrected($line, $amountOfIndentChars, $charakter, $expected): void
    {
        $fixedLine = $this->lineFixer->fixIndentation($line, $amountOfIndentChars, $charakter);
        $this->assertSame($expected, $fixedLine);
    }

    public function lineProvider(): array
    {
        return [
            [
                'line' => 'foo = bar',
                'amountOfIndentChars' => 2,
                'character' => ' ',
                'expected' => '  foo = bar'
            ],
            [
                'line' => '  foo = bar',
                'amountOfIndentChars' => 4,
                'character' => ' ',
                'expected' => '    foo = bar'
            ],
            [
                'line' => "\t\tfoo = bar",
                'amountOfIndentChars' => 4,
                'character' => ' ',
                'expected' => '    foo = bar'
            ],
            [
                'line' => 'foo = bar',
                'amountOfIndentChars' => 2,
                'character' => "\t",
                'expected' => "\t\tfoo = bar"
            ],
            [
                'line' => '  foo = bar',
                'amountOfIndentChars' => 4,
                'character' => "\t",
                'expected' => "\t\t\t\tfoo = bar"
            ],
            [
                'line' => "\t\tfoo = bar",
                'amountOfIndentChars' => 4,
                'character' => "\t",
                'expected' => "\t\t\t\tfoo = bar"
            ]
        ];
    }
}
