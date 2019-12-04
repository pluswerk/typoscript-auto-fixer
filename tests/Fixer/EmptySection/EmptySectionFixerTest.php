<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\EmptySection;

use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue;
use Pluswerk\TypoScriptAutoFixer\Fixer\EmptySection\EmptySectionFixer;

/**
 * Class EmptySectionFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\EmptySection
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\EmptySection\EmptySectionFixer
 */
final class EmptySectionFixerTest extends TestCase
{
    /**
     * @test
     */
    public function lineRangeOfGivenIssueIsRemovedFromFile(): void
    {
        $fileBuilder = $this->createMock(FileBuilder::class);
        $emptySectionFixer = new EmptySectionFixer($fileBuilder);

        $pathName = 'dummy/file.txt';
        $file = $this->createMock(File::class);
        $fixedFile = $this->createMock(File::class);

        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->tokenizeString(
            'test = dummyline' . PHP_EOL
            . 'another = dummy line' . PHP_EOL
            . 'last.dummy = line' . PHP_EOL
            . 'foo.bar {' . PHP_EOL
            . '  empty {' . PHP_EOL
            . '' . PHP_EOL
            . '  }' . PHP_EOL
            . '}' . PHP_EOL
        );
        $issue = new EmptySectionIssue(5, 7);

        $file->expects($this->at(0))->method('removeLines')->with([5,6,7]);

        $file->expects($this->at(1))->method('getPathname')->willReturn($pathName);
        $fileBuilder->expects($this->once())->method('buildFile')->with($pathName)->willReturn($fixedFile);

        $fixedFile = $emptySectionFixer->fixIssue($file, $issue);

        $this->assertNotSame($file, $fixedFile);
    }
}
