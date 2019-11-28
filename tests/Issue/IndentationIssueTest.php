<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Exception\InvalidIndentationCharacterException;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;

/**
 * Class IndentationIssueTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers \Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue
 */
final class IndentationIssueTest extends TestCase
{
    /**
     * @test
     */
    public function amountOfIndentationCharactersCanBeSet(): void
    {
        $issue = new IndentationIssue(14, 4);
        $this->assertSame(4, $issue->amountOfIndentChars());
    }

    /**
     * @test
     */
    public function indentationCharacterTabCanBeSet(): void
    {
        $issue = new IndentationIssue(14, 4, "\t");
        $this->assertSame("\t", $issue->indentationCharacter());
    }

    /**
     * @test
     */
    public function indentationCharacterSpaceCanBeSet(): void
    {
        $issue = new IndentationIssue(14, 4, ' ');
        $this->assertSame(' ', $issue->indentationCharacter());
    }

    /**
     * @test
     */
    public function anExceptionIsThrownIfIndentationCharacterIsNotAllowed(): void
    {
        $this->expectException(InvalidIndentationCharacterException::class);
        $issue = new IndentationIssue(14, 4, 'i');
    }
}
