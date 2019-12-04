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
        $issue = new NestingConsistencyIssue(5, 21, 0, 0);

        $this->assertSame(5, $issue->line());
    }

    /**
     * @test
     */
    public function secondLineCanBeSet(): void
    {
        $issue = new NestingConsistencyIssue(5, 21, 0, 0);

        $this->assertSame(21, $issue->secondLine());
    }

    /**
     * @test
     */
    public function firstEndLineCanBeSet(): void
    {
        $issue = new NestingConsistencyIssue(4, 21, 6, 0);

        $this->assertSame(6, $issue->firstEndLine());
    }

    /**
     * @test
     */
    public function secondEndLineIsCalculated(): void
    {
        $issue = new NestingConsistencyIssue(4, 21, 6, 26);

        $this->assertSame(26, $issue->secondEndLine());
    }
}
