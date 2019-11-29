<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue;

/**
 * Class EmptySectionIssueTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers \Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue
 */
final class EmptySectionIssueTest extends TestCase
{
    /**
     * @test
     */
    public function issueKnowsStartLine(): void
    {
        $issue = new EmptySectionIssue(4, []);
        $this->assertSame(4, $issue->startLine());
    }

    /**
     * @test
     * @dataProvider emptySectionInputProvider
     */
    public function issueKnowsEndLine($input, $startLine, $expected): void
    {
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenizeString($input);
        $issue = new EmptySectionIssue($startLine, $tokens);
        $this->assertSame($expected, $issue->endLine());
    }

    public function emptySectionInputProvider()
    {
        return [
            'first level' => [
                'input' => 'test = dummyline' . PHP_EOL
                           . 'another = dummy line' . PHP_EOL
                           . 'last.dummy = line' . PHP_EOL
                           . 'foo.bar {' . PHP_EOL
                           . '  ' . PHP_EOL
                           . '}' . PHP_EOL,
                'startLine' => 4,
                'expected' => 6
            ],
            'second level' => [
                'input' => 'test = dummyline' . PHP_EOL
                           . 'another = dummy line' . PHP_EOL
                           . 'last.dummy = line' . PHP_EOL
                           . 'foo.bar {' . PHP_EOL
                           . '  empty {' . PHP_EOL
                           . '  }' . PHP_EOL
                           . '}' . PHP_EOL,
                'startLine' => 5,
                'expected' => 6
            ]
        ];
    }
}
