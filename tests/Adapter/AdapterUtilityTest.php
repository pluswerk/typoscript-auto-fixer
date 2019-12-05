<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Adapter;

use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Adapter\AdapterUtility;

/**
 * Class AdapterUtilityTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Adapter
 * @covers \Pluswerk\TypoScriptAutoFixer\Adapter\AdapterUtility
 */
final class AdapterUtilityTest extends TestCase
{
    /**
     * @var AdapterUtility
     */
    private $adapterUtility;

    protected function setUp(): void
    {
        $this->adapterUtility = new AdapterUtility();
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function findEndLineOfNestedStatementInTokens($input, $expectedLine): void
    {
        $tokenizer = new Tokenizer(PHP_EOL);
        $tokens = $tokenizer->tokenizeString($input);

        $this->assertSame($expectedLine, $this->adapterUtility->findEndLineOfNestedStatement(2, $tokens));
    }

    public function inputProvider()
    {
        return [
            [
                'input' => 'first.line = value' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '    sub = sub vlaue' . PHP_EOL
                           . '}' . PHP_EOL,
                'expectedLine' => 4
            ],
            [
                'input' => 'first.line = value' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '    sub = sub vlaue' . PHP_EOL
                           . '    sub2 = sub2 vlaue' . PHP_EOL
                           . '}' . PHP_EOL,
                'expectedLine' => 5
            ],
            [
                'input' => 'first.line = value' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '  sub (' . PHP_EOL
                           . 'multi' . PHP_EOL
                           . 'line' . PHP_EOL
                           . '  value' . PHP_EOL
                           . '  )' . PHP_EOL
                           . '}' . PHP_EOL,
                'expectedLine' => 8
            ]
        ];
    }

    /**
     * @test
     *
     * @param $input
     * @param $expectedLine
     *
     * @return int
     * @dataProvider nestedAppearanveProvider
     */
    public function findTheLineOfFirstNestedAppearanceOfObjectPath($startLine, $objectPath, $input, $expectedLine): void
    {
        $tokenizer = new Tokenizer(PHP_EOL);
        $tokens = $tokenizer->tokenizeString($input);

        $this->assertSame($expectedLine, $this->adapterUtility->findFirstNestedAppearanceOfObjectPath($startLine, $objectPath, $tokens));
    }

    public function nestedAppearanveProvider()
    {
        return [
            [
                'startLine' => 8,
                'objectPath' => 'nested.line',
                'input' => 'first.line = value' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '    sub = sub vlaue' . PHP_EOL
                           . '}' . PHP_EOL
                           . '' . PHP_EOL
                           . 'other = line' . PHP_EOL
                           . '' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '      sub2 = line' . PHP_EOL
                           . '}' . PHP_EOL,
                'expectedLine' => 2
            ],
            [
                'startLine' => 9,
                'objectPath' => 'nested.line',
                'input' => 'first.line = value' . PHP_EOL
                           . 'nested.line.singleLine = single line value' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '    sub = sub vlaue' . PHP_EOL
                           . '}' . PHP_EOL
                           . '' . PHP_EOL
                           . 'other = line' . PHP_EOL
                           . '' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '      sub2 = line' . PHP_EOL
                           . '}' . PHP_EOL,
                'expectedLine' => 3
            ],
            [
                'startLine' => 6,
                'objectPath' => 'nested.line',
                'input' => 'first.line = value' . PHP_EOL
                           . 'nested.line.singleLine = single line value' . PHP_EOL
                           . '' . PHP_EOL
                           . 'other = line' . PHP_EOL
                           . '' . PHP_EOL
                           . 'nested.line {' . PHP_EOL
                           . '      sub2 = line' . PHP_EOL
                           . '}' . PHP_EOL,
                'expectedLine' => 0
            ]
        ];
    }
}
