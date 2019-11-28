<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\OperatorWhitespace;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Exception\NoOperatorFoundException;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\LineFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\OperatorWhitespaceFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

/**
 * Class OperatorWhitespaceFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\OperatorWhitespace
 * @covers Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhiteSpace\OperatorWhitespaceFixer
 * @covers Pluswerk\TypoScriptAutoFixer\Fixer\AbstractFixer
 */
final class OperatorWhitespaceFixerTest extends TestCase
{
    /**
     * @var
     */
    private $operatorWhitespaceFixer;

    /**
     * @var LineFixer|MockObject
     */
    private $lineFixer;

    /**
     * @var FileBuilder|MockObject
     */
    private $fileBuilder;

    protected function setUp(): void
    {
        $this->lineFixer = $this->createMock(LineFixer::class);
        $this->fileBuilder = $this->createMock(FileBuilder::class);
        $this->operatorWhitespaceFixer = new OperatorWhitespaceFixer($this->fileBuilder, $this->lineFixer);
    }

    /**
     * @test
     */
    public function issueLineOfGiveFileIsReplacedWithCorrectString(): void
    {
        $pathName = 'dummy/file.txt';
        $file = $this->createMock(File::class);
        $fixedFile = $this->createMock(File::class);
        $issue = new OperatorWhitespaceIssue(2);

        $line = 'foo=bar';

        $file->expects($this->at(0))->method('readLine')->with(2)->willReturn($line);
        $fixedLine = 'foo = bar';
        $this->lineFixer->expects($this->once())->method('fixOperatorWhitespace')->with($line)->willReturn($fixedLine);
        $file->expects($this->at(1))->method('replaceLine')->with($fixedLine, 2);
        $file->expects($this->at(2))->method('getPathname')->willReturn($pathName);
        $this->fileBuilder->expects($this->once())->method('buildFile')->willReturn($fixedFile);

        $fixedFile = $this->operatorWhitespaceFixer->fixIssue($file, $issue);

        $this->assertNotSame($file, $fixedFile);
    }

    /**
     * @test
     */
    public function ifLineFixerThrowsExceptionFileIsNotWritten(): void
    {
        $file = $this->createMock(File::class);
        $issue = new OperatorWhitespaceIssue(2);

        $line = 'foo.bar';

        $file->expects($this->at(0))->method('readLine')->with(2)->willReturn($line);

        $this->lineFixer->expects($this->once())
                        ->method('fixOperatorWhitespace')
                        ->with($line)
                        ->willThrowException(new NoOperatorFoundException());
        $fixedFile = $this->operatorWhitespaceFixer->fixIssue($file, $issue);
        $this->assertSame($file, $fixedFile);
    }
}
