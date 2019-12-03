<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer;

use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\AbstractFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\FixerFactory;
use Pluswerk\TypoScriptAutoFixer\Fixer\IssueFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;

/**
 * Class IssueFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer
 * @covers Pluswerk\TypoScriptAutoFixer\Fixer\IssueFixer
 */
final class IssueFixerTest extends TestCase
{
    /**
     * @var FileBuilder|MockObject
     */
    private $fileBuilder;

    /**
     * @var FixerFactory|MockObject
     */
    private $fixerFactory;

    /**
     * @var IssueFixer
     */
    private $issueFixer;

    protected function setUp(): void
    {
        $this->fileBuilder = $this->createMock(FileBuilder::class);
        $this->fixerFactory = $this->getMockBuilder(FixerFactory::class)
                                   ->getMock();
        $this->issueFixer = new IssueFixer($this->fileBuilder, $this->fixerFactory);
    }

    /**
     * @test
     */
    public function forAGivenFilePathAllIssuesAreTriedToFix(): void
    {
        $filePath = 'dummy/file/path.txt';
        $file = $this->createMock(File::class);
        $fixedFile = $this->createMock(File::class);
        $issueCollection = $this->createMock(IssueCollection::class);
        $fixedFileIssueCollection = $this->createMock(IssueCollection::class);
        $issue = $this->createMock(AbstractIssue::class);
        $fixer = $this->createMock(AbstractFixer::class);

        $this->fileBuilder->expects($this->once())->method('buildFile')->with($filePath)->willReturn($file);

        $file->expects($this->any())->method('issues')->willReturn($issueCollection);

        $issueCollection->expects($this->at(0))->method('count')->willReturn(1);
        $issueCollection->expects($this->at(1))->method('current')->willReturn($issue);

        $this->fixerFactory->expects($this->once())->method('getFixerByIssue')->with($issue)->willReturn($fixer);

        $fixer->expects($this->once())->method('fixIssue')->with($file, $issue)->willReturn($fixedFile);

        $fixedFile->expects($this->once())->method('issues')->willReturn($fixedFileIssueCollection);

        $fixedFileIssueCollection->expects($this->once())->method('count')->willReturn(0);

        $file->expects($this->at(1))->method('removeNeedlessEmptyLines');

        $this->issueFixer->fixIssuesForFile($filePath);
    }

    /**
     * @test
     */
    public function ifAnIssueCanNotBeFixedItIsTriedMaximumFiftyTimes(): void
    {
        $filePath = 'dummy/file/path.txt';
        $file = $this->createMock(File::class);
        $issueCollection = $this->createMock(IssueCollection::class);
        $issue = $this->createMock(AbstractIssue::class);
        $fixer = $this->createMock(AbstractFixer::class);

        $this->fileBuilder->expects($this->once())->method('buildFile')->with($filePath)->willReturn($file);

        $file->expects($this->exactly(101))->method('issues')->willReturn($issueCollection);

        $issueCollection->expects($this->exactly(51))->method('count')->willReturn(1);
        $issueCollection->expects($this->exactly(50))->method('current')->willReturn($issue);

        $this->fixerFactory->expects($this->exactly(50))->method('getFixerByIssue')->with($issue)->willReturn($fixer);

        $fixer->expects($this->exactly(50))->method('fixIssue')->with($file, $issue)->willReturn($file);

        $file->expects($this->at(101))->method('removeNeedlessEmptyLines');

        $this->issueFixer->fixIssuesForFile($filePath);
    }
}
