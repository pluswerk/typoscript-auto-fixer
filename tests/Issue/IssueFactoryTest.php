<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueFactory;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

/**
 * Class IssueFactoryTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers Pluswerk\TypoScriptAutoFixer\Issue\IssueFactory
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
}
