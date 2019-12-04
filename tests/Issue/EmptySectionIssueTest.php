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
        $issue = new EmptySectionIssue(4, 7);
        $this->assertSame(4, $issue->startLine());
    }

    /**
     * @test
     * @dataProvider emptySectionInputProvider
     */
    public function issueKnowsEndLine($startLine, $endLine, $expected): void
    {
        $issue = new EmptySectionIssue($startLine, $endLine);
        $this->assertSame($expected, $issue->endLine());
    }

    public function emptySectionInputProvider()
    {
        return [
            'first level' => [
                'startLine' => 4,
                'endLine' => 6,
                'expected' => 6
            ]
        ];
    }
}
