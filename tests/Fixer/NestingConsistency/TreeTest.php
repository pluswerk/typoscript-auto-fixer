<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestingConsistency;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NodeCollection;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Tree;

/**
 * Class TreeTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestingConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Tree
 */
final class TreeTest extends TestCase
{
    /**
     * @test
     */
    public function aTreeHoldsNodes(): void
    {
        $nodeCollection = new NodeCollection();
        $tree = new Tree($nodeCollection, 0, 0);

        $this->assertSame($nodeCollection, $tree->nodes());
    }

    /**
     * @test
     */
    public function aTreeHasAFileStartLine(): void
    {
        $nodeCollection = new NodeCollection();
        $tree = new Tree($nodeCollection, 12, 0);

        $this->assertSame(12, $tree->startLine());
    }

    /**
     * @test
     */
    public function aTreeHasAFileEndLine(): void
    {
        $nodeCollection = new NodeCollection();
        $tree = new Tree($nodeCollection, 12, 20);

        $this->assertSame(20, $tree->endLine());
    }
}
