<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueFactory;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue;

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

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsAccessorShouldBeFollowedBySingleSpaceIssueAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(13, null, 'Accessor should be followed by single space.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(13);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsNoWhitespaceAfterOperatorAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(13, null, 'No whitespace after operator.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(13);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsOperatorShouldBeFollowedBySingleSpaceAnOperatorWhitespaceIssuesIsCreated(): void
    {
        $issue = new Issue(13, null, 'Operator should be followed by single space.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new OperatorWhitespaceIssue(13);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueMessageDoesNotMatchAnyTypeNullIsReturned(): void
    {
        $issue = new Issue(13, null, 'Unkown message', Issue::SEVERITY_WARNING, __CLASS__);

        $this->assertNull($this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf4SpacesAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 4 spaces.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 4, ' ');

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf4TabsAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 2 tabs.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 2, "\t");

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf1SpaceAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 1 space.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 1, ' ');

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsExpectedIndentOf1TabAnIndentationIssueIsCreated(): void
    {
        $issue = new Issue(14, null, 'Expected indent of 1 tab.', Issue::SEVERITY_WARNING, __CLASS__);
        $fixerIssue = new IndentationIssue(14, 1, "\t");

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, []));
    }

    /**
     * @test
     */
    public function ifIssueIsEmptyAssignmentBlockAnEmptySectionIssueIsCreated(): void
    {
        $issue      = new Issue(16, null, 'Empty assignment block', Issue::SEVERITY_WARNING, __CLASS__);
        $input      = 'test = dummyline' . PHP_EOL
                 . 'another = dummy line' . PHP_EOL
                 . 'last.dummy = line' . PHP_EOL
                 . 'foo.bar {' . PHP_EOL
                 . '  ' . PHP_EOL
                 . '}' . PHP_EOL;

        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenizeString($input);
        $fixerIssue = new EmptySectionIssue(16, $tokens);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, $tokens));
    }

    /**
     * @test
     */
    public function ifIssueIsCommonPathPrefixIssueAnNestingConsistencyIssueIsCreated(): void
    {
        $issue = new Issue(
            4,
            null,
            'Common path prefix "nest" with assignment to "nest.bar" in line 21. Consider merging them into a nested assignment.',
            Issue::SEVERITY_WARNING,
            __CLASS__
        );

        $input      = 'test = dummyline' . PHP_EOL
                      . 'another = dummy line' . PHP_EOL
                      . 'last.dummy = line' . PHP_EOL
                      . 'nest.bar {'
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

        $fixerIssue = new NestingConsistencyIssue(4, 21, $tokens);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, $tokens));
    }

    /**
     * @test
     */
    public function ifIssueIsAltoughNestedStatementIssueAnNestingConsistencyIssueIsCreated(): void
    {
        $issue = new Issue(
            21,
            null,
            'Assignment to value "nested.another.item", altough nested statement for path "nested" exists at line 4.',
            Issue::SEVERITY_WARNING,
            __CLASS__
        );

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

        $fixerIssue = new NestingConsistencyIssue(4, 21, $tokens);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, $tokens));
    }

    /**
     * @test
     */
    public function ifIssueIsMultipleNestedStatementsIssueAnNestingConsistencyIssueIsCreated(): void
    {
        $issue = new Issue(
            22,
            null,
            'Multiple nested statements for object path "nest". Consider merging them into one statement.',
            Issue::SEVERITY_WARNING,
            __CLASS__
        );

        $input      = 'test = dummyline' . PHP_EOL
                      . 'another = dummy line' . PHP_EOL
                      . 'last.dummy = line' . PHP_EOL
                      . 'nest.test = testvalue' . PHP_EOL
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

        $fixerIssue = new NestingConsistencyIssue(5, 22, $tokens);

        $this->assertEquals($fixerIssue, $this->issueFactory->getIssue($issue, $tokens));
    }
}
