<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

final class Tree
{
    /**
     * @var NodeCollection
     */
    private $nodes;

    /**
     * @var int
     */
    private $startLine;

    /**
     * @var int
     */
    private $endLine;

    /**
     * Tree constructor.
     *
     * @param NodeCollection $nodes
     * @param int            $startLine
     * @param int            $endLine
     */
    public function __construct(NodeCollection $nodes, int $startLine, int $endLine)
    {
        $this->nodes = $nodes;
        $this->startLine = $startLine;
        $this->endLine = $endLine;
    }

    /**
     * @return NodeCollection
     */
    public function nodes(): NodeCollection
    {
        return $this->nodes;
    }

    /**
     * @return int
     */
    public function startLine(): int
    {
        return $this->startLine;
    }

    /**
     * @return int
     */
    public function endLine(): int
    {
        return $this->endLine;
    }
}
