<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Node;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollection;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollectionBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Operator;

/**
 * Class NodeCollectionBuilderTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollectionBuilder
 */
final class NodeCollectionBuilderTest extends TestCase
{
    /**
     * @var NodeCollectionBuilder
     */
    private $nodeCollectionBuilder;

    protected function setUp(): void
    {
        $this->nodeCollectionBuilder = new NodeCollectionBuilder();
    }

    /**
     * @test
     * @dataProvider assignmentProvider
     */
    public function nodeTreeCanBuiltFromSingleLine($string, $value, $operator): void
    {
        $nodes = new NodeCollection();

        $nest = new Node('nest');
        $bar = new Node('bar');
        $line = new Node('line', $value, $operator);

        $nest->addChildNode($bar);
        $bar->addChildNode($line);

        $nodes->add($nest);

        $builtNodes = $this->nodeCollectionBuilder->buildNodeCollectionFromSingleAssignment($string);
        $this->assertEquals($nodes, $builtNodes);
    }

    /**
     * @test
     * @dataProvider multiLineAssignmentsProvider
     */
    public function nodeTreeCanBuiltFromAMultiLineString($string, $nestValue, $nestOperator): void
    {
        $nodes = new NodeCollection();

        $nest = new Node('nest', $nestValue, $nestOperator);
        $bar = new Node('bar');
        $line = new Node('line', 'value12345', Operator::createEqual());
        $foo = new Node('foo');
        $both = new Node('both', '1234', Operator::createEqual());
        $subBoth = new Node('subBoth', 'wert' . PHP_EOL . 'new line' . PHP_EOL . '  indent', Operator::createMultiLine());
        $foo->addChildNode($both);
        $both->addChildNode($subBoth);

        $bar->addChildNode($line);
        $nest->addChildNode($bar);
        $nest->addChildNode($foo);

        $nodes->add($nest);

        $builtNodes = $this->nodeCollectionBuilder->buildNodeCollectionFromMultiLine(explode(PHP_EOL, $string));
        $this->assertEquals($nodes, $builtNodes);
    }

    /**
     * @test
     */
    public function twoNodeCollectionsCanBeMerged(): void
    {
        $nodesA = new NodeCollection();

        $nestA = new Node('nest');
        $barA = new Node('bar');
        $lineA = new Node('line', 'value12345', Operator::createEqual());
        $bothA = new Node('both', 'both value', Operator::createEqual());
        $onlySub = new Node('sub');
        $onlySubSub = new Node('subsub', 'subval', Operator::createEqual());

        $nestA->addChildNode($barA);
        $barA->addChildNode($lineA);
        $barA->addChildNode($bothA);
        $onlySub->addChildNode($onlySubSub);
        $barA->addChildNode($onlySub);

        $nodesA->add($nestA);

        $nodesB = new NodeCollection();

        $nestB = new Node('nest');
        $barB = new Node('bar', 'test', Operator::createEqual());
        $lineB = new Node('lineB', 'hklj', Operator::createEqual());
        $fooB = new Node('foo', 'value89765', Operator::createEqual());
        $bothB = new Node('both');
        $subBothB = new Node('subBoth', 'sub both value', Operator::createEqual());
        $onlyVal = new Node('sub', 'val', Operator::createEqual());

        $bothB->addChildNode($subBothB);
        $nestB->addChildNode($barB);
        $nestB->addChildNode($lineB);
        $barB->addChildNode($fooB);
        $barB->addChildNode($onlyVal);
        $barB->addChildNode($bothB);

        $nodesB->add($nestB);

        $expectedNodes = new NodeCollection();

        $expectedNest = new Node('nest');
        $expectedBar = new Node('bar', 'test', Operator::createEqual());
        $expectedLineA = new Node('line', 'value12345', Operator::createEqual());
        $expectedLineB = new Node('lineB', 'hklj', Operator::createEqual());
        $expectedFoo = new Node('foo', 'value89765', Operator::createEqual());
        $expectedBoth = new Node('both', 'both value', Operator::createEqual());
        $expectedSubBoth = new Node('subBoth', 'sub both value', Operator::createEqual());
        $expectedOnlyValOnlySub = new Node('sub', 'val', Operator::createEqual());
        $expectedOnlyValOnlySubSub = new Node('subsub', 'subval', Operator::createEqual());

        $expectedBoth->addChildNode($expectedSubBoth);
        $expectedNest->addChildNode($expectedBar);
        $expectedNest->addChildNode($expectedLineB);
        $expectedBar->addChildNode($expectedLineA);
        $expectedBar->addChildNode($expectedBoth);
        $expectedBar->addChildNode($expectedFoo);
        $expectedOnlyValOnlySub->addChildNode($expectedOnlyValOnlySubSub);
        $expectedBar->addChildNode($expectedOnlyValOnlySub);

        $expectedNodes->add($expectedNest);

        $actualNodes = $this->nodeCollectionBuilder->mergeNodeCollections($nodesA, $nodesB);

        $this->assertEquals($expectedNodes, $actualNodes);
    }

    /**
     * @test
     */
    public function mergingNodeCollectionsThrowExceptionWithOverwrite(): void
    {
        $nodesA = new NodeCollection();

        $nestA = new Node('nest');
        $barA = new Node('bar', 'othertest', Operator::createEqual());
        $lineA = new Node('line', 'value12345', Operator::createEqual());
        $bothA = new Node('both', 'both value', Operator::createEqual());
        $onlySub = new Node('sub');
        $onlySubSub = new Node('subsub', 'subval', Operator::createEqual());

        $nestA->addChildNode($barA);
        $barA->addChildNode($lineA);
        $barA->addChildNode($bothA);
        $onlySub->addChildNode($onlySubSub);
        $barA->addChildNode($onlySub);

        $nodesA->add($nestA);

        $nodesB = new NodeCollection();

        $nestB = new Node('nest');
        $barB = new Node('bar', 'test', Operator::createEqual());
        $lineB = new Node('lineB', 'hklj', Operator::createEqual());
        $fooB = new Node('foo', 'value89765', Operator::createEqual());
        $bothB = new Node('both');
        $subBothB = new Node('subBoth', 'sub both value', Operator::createEqual());
        $onlyVal = new Node('sub', 'val', Operator::createEqual());

        $bothB->addChildNode($subBothB);
        $nestB->addChildNode($barB);
        $nestB->addChildNode($lineB);
        $barB->addChildNode($fooB);
        $barB->addChildNode($onlyVal);
        $barB->addChildNode($bothB);

        $nodesB->add($nestB);

        $expectedNodes = new NodeCollection();

        $expectedNest = new Node('nest');
        $expectedBar = new Node('bar', 'test', Operator::createEqual());
        $expectedLineA = new Node('line', 'value12345', Operator::createEqual());
        $expectedLineB = new Node('lineB', 'hklj', Operator::createEqual());
        $expectedFoo = new Node('foo', 'value89765', Operator::createEqual());
        $expectedBoth = new Node('both', 'both value', Operator::createEqual());
        $expectedSubBoth = new Node('subBoth', 'sub both value', Operator::createEqual());
        $expectedOnlyValOnlySub = new Node('sub', 'val', Operator::createEqual());
        $expectedOnlyValOnlySubSub = new Node('subsub', 'subval', Operator::createEqual());

        $expectedBoth->addChildNode($expectedSubBoth);
        $expectedNest->addChildNode($expectedBar);
        $expectedNest->addChildNode($expectedLineB);
        $expectedBar->addChildNode($expectedLineA);
        $expectedBar->addChildNode($expectedBoth);
        $expectedBar->addChildNode($expectedFoo);
        $expectedOnlyValOnlySub->addChildNode($expectedOnlyValOnlySubSub);
        $expectedBar->addChildNode($expectedOnlyValOnlySub);

        $expectedNodes->add($expectedNest);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Overwrite assignment!');
        $actualNodes = $this->nodeCollectionBuilder->mergeNodeCollections($nodesA, $nodesB);

        $this->assertEquals($expectedNodes, $actualNodes);
    }

    public function assignmentProvider(): array
    {
        return [
            [
                'string' => 'nest.bar.line = value12345',
                'value' => 'value12345',
                'operator' => Operator::createEqual()
            ],
            [
                'string' => 'nest.bar.line >',
                'value' => '',
                'operator' => Operator::createDelete()
            ],
            [
                'string' => 'nest.bar.line := addToList(4,5)',
                'value' => 'addToList(4,5)',
                'operator' => Operator::createModification()
            ],
            [
                'string' => 'nest.bar.line < copy.from',
                'value' => 'copy.from',
                'operator' => Operator::createCopy()
            ],
            [
                'string' => 'nest.bar.line =< copy.from',
                'value' => 'copy.from',
                'operator' => Operator::createReference()
            ],
            [
                'string' => 'nest.bar.line ('
                            . PHP_EOL . 'value12345'
                            . PHP_EOL . 'new line value'
                            . PHP_EOL . '  indent'
                            . PHP_EOL . ')',
                'value' => 'value12345'
                            . PHP_EOL . 'new line value'
                            . PHP_EOL . '  indent',
                'operator' => Operator::createMultiLine()
            ]
        ];
    }

    public function multiLineAssignmentsProvider()
    {
        return [
            [
                'string' => '
nest = 1
nest {
  bar {
    line = value12345
    
  }
  foo.both = 1234
  foo.both {
    subBoth (
wert
new line
  indent
    )
  }
}',
                'nestValue' => '1',
                'operator' => Operator::createEqual()
            ],
            [
                'string' => '
nest (
multi
line
  first
  level
)
nest {
  bar {
    line = value12345
    
  }
  foo.both = 1234
  foo.both {
    subBoth (
wert
new line
  indent
    )
  }
}',
                'nestValue' => 'multi
line
  first
  level',
                'operator' => Operator::createMultiLine()
            ]
        ];
    }
}
