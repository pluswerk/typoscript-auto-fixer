<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueFactory;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;

/**
 * Class IssueFactoryTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers \Pluswerk\TypoScriptAutoFixer\Issue\IssueFactory
 */
final class IssueFactoryTest extends TestCase
{
    /**
     * @var IssueFactory
     */
    private $issueFactory;

    protected function setUp(): void
    {
        $this->issueFactory = new IssueFactory();
    }

    /**
     * @test
     */
    public function ifIssueIsNoWhitespaceAfterObjectAccessorIssueAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(12, null, 'No whitespace after object accessor.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(12);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsAccessorShouldBeFollowedBySingleSpaceIssueAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(13, null, 'Accessor should be followed by single space.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(13);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsNoWhitespaceAfterOperatorAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(13, null, 'No whitespace after operator.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(13);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsOperatorShouldBeFollowedBySingleSpaceAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(13, null, 'Operator should be followed by single space.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(13);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueMessageDoesNotMatchAnyTypeNullIsReturned(): void
    {
        $issue = new Issue(13, null, 'Unkown message', Issue::SEVERITY_WARNING, __CLASS__);

        $this->assertNull($this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf4SpacesAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 4 spaces.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 4, ' ');

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf4TabsAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 2 tabs.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 2, "\t");

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf1SpaceAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 1 space.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 1, ' ');

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf1TabAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 1 tab.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 1, "\t");

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue));
    }
}
