<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use DG\BypassFinals;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NestingConsistencyFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollection;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollectionBuilder;
use Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue;

/**
 * Class NestingConsistencyFixerTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NestingConsistencyFixer
 */
final class NestingConsistencyFixerTest extends TestCase
{
    /**
     * @test
     * @dataProvider issueProvider
     */
    public function nestingConsistencyIsFixed($issue): void
    {
        /** @var FileBuilder|MockObject $fileBuilder */
        $fileBuilder = $this->createMock(FileBuilder::class);
        /** @var NodeCollectionBuilder|MockObject $nodeCollectionBuilder */
        $nodeCollectionBuilder = $this->createMock(NodeCollectionBuilder::class);
        $nestingConsistencyFixer = new NestingConsistencyFixer($fileBuilder, $nodeCollectionBuilder);

        $nodeCollectionA = new NodeCollection();
        $nodeCollectionB = new NodeCollection();
        $nodeCollectionResult = $this->createMock(NodeCollection::class);
        $insertString = 'insert string' . PHP_EOL;

        $pathName = 'dummy/file.txt';
        $file = $this->createMock(File::class);
        $fixedFile = $this->createMock(File::class);

        $file->expects($this->exactly(9))
             ->method('readLine')
             ->withConsecutive(
                 [4],
                 [5],
                 [6],
                 [21],
                 [22],
                 [23],
                 [24],
                 [25],
                 [26]
             )
             ->willReturnOnConsecutiveCalls(
                 ...[
                     'nest.bar {' . PHP_EOL,
                     '  foo = value1234' . PHP_EOL,
                     '}' . PHP_EOL,
                     'nest.bar {' . PHP_EOL,
                     '  definition = value' . PHP_EOL,
                     '  another {' . PHP_EOL,
                     '    level = value2' . PHP_EOL,
                     '  }' . PHP_EOL,
                     '}' . PHP_EOL
                 ]
             );

        $nodeCollectionBuilder->expects($this->at(0))
                              ->method('buildNodeCollectionFromMultiLine')
                              ->with(
                                  [
                                      'nest.bar {' . PHP_EOL,
                                      '  foo = value1234' . PHP_EOL,
                                      '}' . PHP_EOL
                                  ]
                              )->willReturn($nodeCollectionA);

        $nodeCollectionBuilder->expects($this->at(1))
                              ->method('buildNodeCollectionFromMultiLine')
                              ->with(
                                  [
                                      'nest.bar {' . PHP_EOL,
                                      '  definition = value' . PHP_EOL,
                                      '  another {' . PHP_EOL,
                                      '    level = value2' . PHP_EOL,
                                      '  }' . PHP_EOL,
                                      '}' . PHP_EOL
                                  ]
                              )->willReturn($nodeCollectionB);

        $nodeCollectionBuilder->expects($this->at(2))
                              ->method('mergeNodeCollections')
                              ->with($nodeCollectionA, $nodeCollectionB)
                              ->willReturn($nodeCollectionResult);

        $file->expects($this->at(9))->method('removeLines')->with([21,22,23,24,25,26]);

        $nodeCollectionResult->expects($this->once())->method('__toString')->willReturn($insertString);

        $file->expects($this->at(10))->method('insertStringToFile')->with(21, $insertString);

        $file->expects($this->at(11))->method('removeLines')->with([4,5,6]);

        $file->expects($this->at(12))->method('getPathname')->willReturn($pathName);
        $fileBuilder->expects($this->once())->method('buildFile')->with($pathName)->willReturn($fixedFile);

        $fixedFile = $nestingConsistencyFixer->fixIssue($file, $issue);

        $this->assertNotSame($file, $fixedFile);
    }

    public function issueProvider()
    {
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

        return [
            [
                'issue' => new NestingConsistencyIssue(4, 21, $tokens)
            ],
            [
                'issue' => new NestingConsistencyIssue(21, 4, $tokens)
            ]
        ];
    }
}
