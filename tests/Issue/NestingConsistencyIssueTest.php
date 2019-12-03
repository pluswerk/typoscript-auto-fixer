<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue;

/**
 * Class NestingConsistencyIssueTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers \Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue
 */
final class NestingConsistencyIssueTest extends TestCase
{
    /**
     * @test
     */
    public function firstLineCanBeSet(): void
    {
        $issue = new NestingConsistencyIssue(5, 21, []);

        $this->assertSame(5, $issue->line());
    }

    /**
     * @test
     */
    public function secondLineCanBeSet(): void
    {
        $issue = new NestingConsistencyIssue(5, 21, []);

        $this->assertSame(21, $issue->secondLine());
    }

    /**
     * @test
     */
    public function firstEndLineIsCalculated(): void
    {
        $input      = 'test = dummyline' . PHP_EOL
                      . 'another = dummy line' . PHP_EOL
                      . 'last.dummy = line' . PHP_EOL
                      . 'nest.bar {' . PHP_EOL
                      . '  foo = value1234' . PHP_EOL
                      . '}' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . 'nest.bar {' . PHP_EOL
                      . '  definition = value' . PHP_EOL
                      . '  another {' . PHP_EOL
                      . '    level = value2' . PHP_EOL
                      . '  }' . PHP_EOL
                      . '}' . PHP_EOL
                      . '' . PHP_EOL;

        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenizeString($input);

        $issue = new NestingConsistencyIssue(4, 21, $tokens);

        $this->assertSame(6, $issue->firstEndLine());
    }

    /**
     * @test
     */
    public function secondEndLineIsCalculated(): void
    {
        $input      = 'test = dummyline' . PHP_EOL
                      . 'another = dummy line' . PHP_EOL
                      . 'last.dummy = line' . PHP_EOL
                      . 'nest.bar {' . PHP_EOL
                      . '  foo = value1234' . PHP_EOL
                      . '}' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . '' . PHP_EOL
                      . 'nest.bar {' . PHP_EOL
                      . '  definition = value' . PHP_EOL
                      . '  another {' . PHP_EOL
                      . '    level = value2' . PHP_EOL
                      . '  }' . PHP_EOL
                      . '}' . PHP_EOL
                      . '' . PHP_EOL;

        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenizeString($input);

        $issue = new NestingConsistencyIssue(4, 21, $tokens);

        $this->assertSame(26, $issue->secondEndLine());
    }
}
