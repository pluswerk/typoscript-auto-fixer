<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\Indentation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\IndentationFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\LineFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;

/**
 * Class OperatorWhitespaceFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\OperatorWhitespace
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\IndentationFixer
 */
final class IndentationFixerTest extends TestCase
{
    /**
     * @var IndentationFixer
     */
    private $indentationFixer;

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
        $this->indentationFixer = new IndentationFixer($this->fileBuilder, $this->lineFixer);
    }

    /**
     * @test
     */
    public function issueLineOfGiveFileIsReplacedWithCorrectString(): void
    {
        $pathName = 'dummy/file.txt';
        $file = $this->createMock(File::class);
        $fixedFile = $this->createMock(File::class);
        $issue = new IndentationIssue(2, 2);

        $line = 'foo = bar';

        $file->expects($this->at(0))->method('readLine')->with(2)->willReturn($line);
        $fixedLine = '  foo = bar';

        $this->lineFixer->expects($this->once())->method('fixIndentation')->with($line, 2, ' ')->willReturn($fixedLine);

        $file->expects($this->at(1))->method('replaceLine')->with($fixedLine, 2);
        $file->expects($this->at(2))->method('getPathname')->willReturn($pathName);
        $this->fileBuilder->expects($this->once())->method('buildFile')->willReturn($fixedFile);

        $fixedFile = $this->indentationFixer->fixIssue($file, $issue);

        $this->assertNotSame($file, $fixedFile);
    }
}
