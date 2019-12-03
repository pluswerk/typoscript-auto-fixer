<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Exception\NodeTitleMustNotBeEmptyException;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Node;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollection;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Operator;

/**
 * Class NodeTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Node
 */
final class NodeTest extends TestCase
{
    /**
     * @test
     */
    public function aNodeCanHoldChildNodes(): void
    {
        $parentNode = new Node('parent');
        $childNode1 = new Node('child1');
        $childNode2 = new Node('child2');

        $parentNode->addChildNode($childNode1);
        $parentNode->addChildNode($childNode2);

        $this->assertSame($childNode1, $parentNode->children()->current());
        $parentNode->children()->next();
        $this->assertSame($childNode2, $parentNode->children()->current());
    }

    /**
     * @test
     */
    public function aNodeHasATitle(): void
    {
        $node = new Node('title');
        $this->assertSame('title', $node->identifier());
    }

    /**
     * @test
     */
    public function theNodeTitleMustNotBeEmpty(): void
    {
        $this->expectException(NodeTitleMustNotBeEmptyException::class);
        new Node('');
    }

    /**
     * @test
     */
    public function nodeCanBeCheckedForChildren(): void
    {
        $node = new Node('title');
        $childNode1 = new Node('child1');
        $node->addChildNode($childNode1);
        $this->assertTrue($node->hasChildren());
        $this->assertFalse($childNode1->hasChildren());
    }

    /**
     * @test
     */
    public function aNodeCanHaveAValue(): void
    {
        $node = new Node('title', 'value');
        $this->assertSame('value', $node->value());
    }

    /**
     * @test
     */
    public function nodeCanBeCheckedForValue(): void
    {
        $node1 = new Node('title', 'value');
        $node2 = new Node('title');
        $this->assertTrue($node1->hasValue());
        $this->assertFalse($node2->hasValue());
    }

    /**
     * @test
     */
    public function nodeCanHaveAnOperator(): void
    {
        $operator = Operator::createEqual();
        $node = new Node('title', 'value', $operator);
        $this->assertSame($operator, $node->operator());
    }

    /**
     * @test
     */
    public function theChildrenCanBeUpdated(): void
    {
        $childNode = new Node('testA');

        $collection = new NodeCollection();
        $collection->add(new Node('testB'));

        $node = new Node('parent');
        $node->addChildNode($childNode);

        $node->updateCollection($collection);

        $this->assertSame($collection, $node->children());
    }

    /**
     * @test
     * @dataProvider nodeToStringProvider
     */
    public function canBePrintedAsString($node, $expected): void
    {
        $this->assertSame($expected, (string) $node);
    }

    public function nodeToStringProvider()
    {
        return [
            [
                'node' => new Node('foo', 'bar', Operator::createEqual()),
                'expected' => 'foo = bar' . PHP_EOL
            ],
            [
                'node' => new Node('foo', '', Operator::createDelete()),
                'expected' => 'foo >' . PHP_EOL
            ],
            [
                'node' => new Node('foo', 'copy.path', Operator::createCopy()),
                'expected' => 'foo < copy.path' . PHP_EOL
            ],
            [
                'node' => new Node('foo', 'reference.path', Operator::createReference()),
                'expected' => 'foo =< reference.path' . PHP_EOL
            ],
            [
                'node' => new Node('foo', 'appendToList(1,3)', Operator::createModification()),
                'expected' => 'foo := appendToList(1,3)' . PHP_EOL
            ],
            [
                'node' => new Node('foo', '(' . PHP_EOL . 'multi' . PHP_EOL . '  line' . PHP_EOL . ')', Operator::createMultiLine()),
                'expected' => 'foo (' . PHP_EOL . 'multi' . PHP_EOL . '  line' . PHP_EOL . ')' . PHP_EOL
            ]
        ];
    }

    /**
     * @test
     */
    public function childNodesArePrintedWithIndentation(): void
    {
        $node = new Node('foo', 'bar', Operator::createEqual(), 2);
        $subNode = new Node('sub', 'node', Operator::createEqual());

        $node->addChildNode($subNode);

        $expected = '    foo = bar' . PHP_EOL
                    . '    foo {' . PHP_EOL
                    . '      sub = node' . PHP_EOL
                    . '    }' . PHP_EOL;

        $this->assertSame($expected, (string) $node);
    }

    /**
     * @test
     */
    public function childNodesArePrintedWithIndentationBesideMultiLineValues(): void
    {
        $node = new Node('foo', 'bar', Operator::createEqual(), 2);
        $subNode = new Node(
            'sub',
            '(' . PHP_EOL . 'multi' . PHP_EOL . '  line' . PHP_EOL . ')',
            Operator::createMultiLine()
        );

        $node->addChildNode($subNode);

        $expected = '    foo = bar' . PHP_EOL
                    . '    foo {' . PHP_EOL
                    . '      sub (' . PHP_EOL
                    . 'multi' . PHP_EOL
                    . '  line' . PHP_EOL
                    . ')' . PHP_EOL
                    . '    }' . PHP_EOL;

        $this->assertSame($expected, (string) $node);
    }

    /**
     * @test
     */
    public function aNodeHasALevel(): void
    {
        $node = new Node('foo', 'bar', Operator::createEqual(), 3);
        $this->assertSame(3, $node->level());
    }

    /**
     * @test
     */
    public function theNodeLevelIsUpdatedWithAddingANodeToAnotherAsChild(): void
    {
        $node = new Node('foo', 'bar', Operator::createEqual());
        $subNode = new Node('sub', 'cal', Operator::createEqual());

        $node->addChildNode($subNode);

        $this->assertSame(1, $subNode->level());
    }

    /**
     * @test
     */
    public function theNodeLevelIsUpdatedForAllChildrenWithUpdatingParent(): void
    {
        $node = new Node('foo', 'bar', Operator::createEqual());
        $subNode = new Node('sub', 'cal', Operator::createEqual());
        $subSubNode = new Node('sub', 'cal', Operator::createEqual());

        $node->addChildNode($subNode);
        $subNode->addChildNode($subSubNode);

        $node->updateLevel(3);

        $this->assertSame(4, $subNode->level());
        $this->assertSame(5, $subSubNode->level());
    }
}
