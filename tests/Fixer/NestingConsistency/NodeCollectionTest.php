<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollection;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Node;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Operator;

/**
 * Class NodeCollectionTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollection
 */
final class NodeCollectionTest extends TestCase
{
    /**
     * @var NodeCollection
     */
    private $nodeCollection;

    protected function setUp(): void
    {
        $this->nodeCollection = new NodeCollection();
    }

    /**
     * @test
     */
    public function aNodeCanBeAddedToCollection(): void
    {
        $node = new Node('title');
        $this->nodeCollection->add($node);
        $this->assertSame($node, $this->nodeCollection->current());
        $this->assertSame(1, $this->nodeCollection->count());
    }

    /**
     * @test
     */
    public function multipleNodesCanBeAddedToCollection(): void
    {
        $node1 = new Node('title1');
        $node2 = new Node('title2');
        $node3 = new Node('title3');

        $this->nodeCollection->add($node1);
        $this->nodeCollection->add($node2);
        $this->nodeCollection->add($node3);

        $this->assertSame($node1, $this->nodeCollection->current());
        $this->nodeCollection->next();
        $this->assertSame($node2, $this->nodeCollection->current());
        $this->nodeCollection->next();
        $this->assertSame($node3, $this->nodeCollection->current());
    }

    /**
     * @test
     */
    public function theNodesCanBeIterated(): void
    {
        $nodes[0] = new Node('title1');
        $nodes[1] = new Node('title2');
        $nodes[2] = new Node('title3');

        $this->nodeCollection->add($nodes[0]);
        $this->nodeCollection->add($nodes[1]);
        $this->nodeCollection->add($nodes[2]);

        foreach ($this->nodeCollection as $key => $addedNodes) {
            $this->assertSame($nodes[$key], $addedNodes, 'node key: ' . $key);
        }
    }

    /**
     * @test
     */
    public function aCollectionCanBeCheckedForASpecialNodeWhichExists(): void
    {
        $node = new Node('test');
        $collection = new NodeCollection();

        $collection->add($node);

        $this->assertTrue($collection->hasNode('test'));
    }

    /**
     * @test
     */
    public function aCollectionCanBeCheckedForASpecialNodeWhichNotExists(): void
    {
        $node = new Node('test');
        $collection = new NodeCollection();

        $collection->add($node);

        $this->assertFalse($collection->hasNode('not'));
    }

    /**
     * @test
     */
    public function aNodeCanBeFetchedByTitleIfItExists(): void
    {
        $node = new Node('test');
        $collection = new NodeCollection();

        $collection->add($node);

        $this->assertSame($node, $collection->getNode('test'));
    }

    /**
     * @test
     */
    public function aNodeCantBeFetchedByTitleIfItDoesNotExists(): void
    {
        $node = new Node('test');
        $collection = new NodeCollection();

        $collection->add($node);

        $this->assertNull($collection->getNode('not'));
    }
    
    /**
     * @test
     */
    public function aNodeCanBeUpdated(): void
    {
        $nodeA = new Node('test');
        $nodeB = new Node('test');
        $subNode = new Node('subnode');
        $nodeB->addChildNode($subNode);

        $col = new NodeCollection();
        $col->add($nodeA);

        $col->add($nodeB);

        $this->assertSame($nodeB, $col->getNode('test'));
    }

    /**
     * @test
     */
    public function collectionCanBeConvertedToString(): void
    {
        $nodes = new NodeCollection();

        $nest = new Node('nest');
        $bar = new Node('bar', 'test', Operator::createEqual());
        $linea = new Node('line', 'value12345', Operator::createEqual());
        $lineb = new Node('lineB', 'hklj', Operator::createEqual());
        $foo = new Node('foo', 'value89765', Operator::createEqual());
        $both = new Node('both', 'both value', Operator::createEqual());
        $subBoth = new Node('subBoth', 'sub both value', Operator::createEqual());
        $onlyValOnlySub = new Node('sub', 'val', Operator::createEqual());
        $onlyValOnlySubSub = new Node('subsub', '(' . PHP_EOL . 'multi' . PHP_EOL . '  line' . PHP_EOL . ')', Operator::createEqual());

        $both->addChildNode($subBoth);
        $nest->addChildNode($bar);
        $nest->addChildNode($lineb);
        $bar->addChildNode($linea);
        $bar->addChildNode($both);
        $bar->addChildNode($foo);
        $onlyValOnlySub->addChildNode($onlyValOnlySubSub);
        $bar->addChildNode($onlyValOnlySub);

        $nodes->add($nest);

        $expected = 'nest {' . PHP_EOL
                    . '  bar = test' . PHP_EOL
                    . '  bar {' . PHP_EOL
                    . '    line = value12345' . PHP_EOL
                    . '    both = both value' . PHP_EOL
                    . '    both {' . PHP_EOL
                    . '      subBoth = sub both value' . PHP_EOL
                    . '    }' . PHP_EOL
                    . '    foo = value89765' . PHP_EOL
                    . '    sub = val' . PHP_EOL
                    . '    sub {' . PHP_EOL
                    . '      subsub = ' . '(' . PHP_EOL . 'multi' . PHP_EOL . '  line' . PHP_EOL . ')' . PHP_EOL
                    . '    }' . PHP_EOL
                    . '  }' . PHP_EOL
                    . '  lineB = hklj' . PHP_EOL
                    . '}' . PHP_EOL;

        $this->assertSame($expected, (string) $nodes);
    }
}
