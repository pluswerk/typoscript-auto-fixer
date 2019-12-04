<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Issue;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;

final class DummyIssue extends AbstractIssue
{
}

/**
 * Class IssueCollectionTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection
 */
final class IssueCollectionTest extends TestCase //phpcs:ignore
{
    /**
     * @var \Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection
     */
    private $issueCollection;

    protected function setUp(): void
    {
        $this->issueCollection= new IssueCollection();
    }

    /**
     * @test
     */
    public function ifIssueIsAddedToCollectionItCanBeAccessed(): void
    {
        $fixerIssue = $this->createMock(AbstractIssue::class);

        $this->issueCollection->add($fixerIssue);

        $this->assertSame($fixerIssue, $this->issueCollection->current());
    }

    /**
     * @test
     * @dataProvider issuesProvider
     */
    public function issuesAreSortedByLine($fixerIssueA, $fixerIssueB, $lineA, $lineB): void
    {
        $this->issueCollection->add($fixerIssueA);
        $this->issueCollection->add($fixerIssueB);

        $this->assertSame($lineA, $this->issueCollection->current()->line());
        $this->issueCollection->next();
        $this->assertSame($lineB, $this->issueCollection->current()->line());
    }
    
    /**
     * @test
     */
    public function overAllIssuesCanBeIterated(): void
    {
        $fixerIssueA = new TestIssue(5);
        $fixerIssueB = new TestIssue(17);

        $this->issueCollection->add($fixerIssueA);
        $this->issueCollection->add($fixerIssueB);

        $result = [
            $fixerIssueA,
            $fixerIssueB
        ];

        $this->assertSame(count($result), $this->issueCollection->count());

        foreach ($this->issueCollection as $key => $issue) {
            $this->assertSame($result[$key], $issue);
        }
    }

    public function issuesProvider(): array
    {
        $fixerIssueA = new TestIssue(17);
        $fixerIssueB = new TestIssue(5);
        $fixerIssueC = new TestIssue(5);
        return [
            [
                'issueA' => $fixerIssueA,
                'issueB' => $fixerIssueB,
                'lineA' => 5,
                'line' => 17
            ],
            [
                'issueA' => $fixerIssueB,
                'issueB' => $fixerIssueA,
                'lineA' => 5,
                'line' => 17
            ],
            [
                'issueA' => $fixerIssueB,
                'issueB' => $fixerIssueC,
                'lineA' => 5,
                'line' => 5
            ],
        ];
    }
}
