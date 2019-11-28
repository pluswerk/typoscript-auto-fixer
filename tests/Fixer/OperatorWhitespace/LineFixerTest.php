<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\OperatorWhitespace;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\LineFixer;

/**
 * Class LineFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\OperatorWhitespace
 * @covers Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhiteSpace\LineFixer
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
     * @dataProvider operatorLineProvider
     */
    public function operatorIsWrappedByExactlyOneWhitespaceEachSide($line, $expected): void
    {
        $this->assertSame($expected, $this->lineFixer->fixOperatorWhitespace($line));
    }

    public function operatorLineProvider()
    {
        return [
            'operator: \'foo= bar\'' => [
                'line' => 'foo= bar',
                'expected' => 'foo = bar'
            ],
            'operator: \'foo =bar\'' => [
                'line' => 'foo =bar',
                'expected' => 'foo = bar'
            ],
            'operator: \'foo=bar\'' => [
                'line' => 'foo=bar',
                'expected' => 'foo = bar'
            ],
            'operator: \'foo=<bar\'' => [
                'line' => 'foo=<bar',
                'expected' => 'foo =< bar'
            ],
            'operator: \'foo=< bar\'' => [
                'line' => 'foo=< bar',
                'expected' => 'foo =< bar'
            ],
            'operator: \'foo =<bar\'' => [
                'line' => 'foo =<bar',
                'expected' => 'foo =< bar'
            ],
            'operator: \'foo:=bar\'' => [
                'line' => 'foo:=bar',
                'expected' => 'foo := bar'
            ],
            'operator: \'foo:= bar\'' => [
                'line' => 'foo:= bar',
                'expected' => 'foo := bar'
            ],
            'operator: \'foo :=bar\'' => [
                'line' => 'foo :=bar',
                'expected' => 'foo := bar'
            ],
            'operator: \'foo<bar\'' => [
                'line' => 'foo<bar',
                'expected' => 'foo < bar'
            ],
            'operator: \'foo< bar\'' => [
                'line' => 'foo< bar',
                'expected' => 'foo < bar'
            ],
            'operator: \'foo <bar\'' => [
                'line' => 'foo <bar',
                'expected' => 'foo < bar'
            ],
            'operator: \'foo>\'' => [
                'line' => 'foo>',
                'expected' => 'foo >'
            ],
            'operator: \'    foo <bar\'' => [
                'line' => '    foo <bar',
                'expected' => '    foo < bar'
            ],
            'operator: \'    foo>\'' => [
                'line' => '    foo>',
                'expected' => '    foo >'
            ],
            'no operator' => [
                'line' => '    foo.bar',
                'expected' => '    foo.bar'
            ]
        ];
    }
}
