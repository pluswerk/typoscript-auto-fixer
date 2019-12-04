<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Adapter\Linter;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;

/**
 * Class FileBuilderTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Issue
 * @covers Pluswerk\TypoScriptAutoFixer\FileBuilder
 */
final class FileBuilderTest extends TestCase
{
    /**
     * @var \Pluswerk\TypoScriptAutoFixer\Adapter\Linter
     */
    private $linter;

    /**
     * @var FileBuilder
     */
    private $fileBuilder;

    protected function setUp(): void
    {
        $this->linter = $this->createMock(Linter::class);
        $this->fileBuilder = new FileBuilder($this->linter);
    }

    /**
     * @test
     */
    public function aFileWithIssueCollectionIsBuilt(): void
    {
        $filePath = __DIR__ . '/Fixtures/test.txt';
        $expectedFile = new File(__DIR__ . '/Fixtures/test.txt');

        $issueCollection = new IssueCollection();
        $this->linter->expects($this->once())->method('lint')->with($filePath)->willReturn($issueCollection);

        $this->assertEquals($expectedFile, $this->fileBuilder->buildFile($filePath));
    }

    /**
     * @test
     */
    public function theBuiltFileHoldsTheIssueCollectionWhichIsBuildByLinter(): void
    {
        $filePath = __DIR__ . '/Fixtures/test.txt';
        $expectedFile = new File(__DIR__ . '/Fixtures/test.txt');

        $issueCollection = new IssueCollection();
        $issue = $this->createMock(AbstractIssue::class);
        $issueCollection->add($issue);
        $expectedFile->updateIssueCollection($issueCollection);

        $this->linter->expects($this->once())->method('lint')->with($filePath)->willReturn($issueCollection);

        $this->assertEquals($expectedFile, $this->fileBuilder->buildFile($filePath));
    }
}
