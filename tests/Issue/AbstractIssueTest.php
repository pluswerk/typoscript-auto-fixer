<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;

final class TestIssue extends AbstractIssue
{
}

/**
 * Class AbstractIssueTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers \Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue
 */
final class AbstractIssueTest extends TestCase //phpcs:ignore
{
    /**
     * @test
     */
    public function aLineCanBeSet(): void
    {
        $issue = new TestIssue(4);
        $this->assertSame(4, $issue->line());
    }
}
