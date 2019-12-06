<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration;
use Pluswerk\TypoScriptAutoFixer\Exception\NodeTitleMustNotBeEmptyException;

final class Node
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var NodeCollection
     */
    private $children;

    /**
     * @var string
     */
    private $value;

    /**
     * @var Operator
     */
    private $operator;

    /**
     * @var int
     */
    private $level;

    /**
     * Node constructor.
     *
     * @param string   $identifier
     * @param string   $value
     * @param Operator $operator
     * @param int      $level
     */
    public function __construct(string $identifier, string $value = '', Operator $operator = null, int $level = 0)
    {
        if ($identifier === '') {
            throw new NodeTitleMustNotBeEmptyException('Node title must not be empty!');
        }
        $this->identifier = $identifier;

        if ($value !== '') {
            $this->value = $value;
        }
        if ($operator !== null) {
            $this->operator = $operator;
        }

        $this->level = $level;

        $this->children = new NodeCollection();
    }

    /**
     * @param Node $childNode
     */
    public function addChildNode(Node $childNode): void
    {
        $childNode->updateLevel($this->level + 1);
        $this->children->add($childNode);
    }

    /**
     * @return NodeCollection
     */
    public function children(): NodeCollection
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function identifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children->count() > 0;
    }

    /**
     * @return string|null
     */
    public function value(): ?string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        if (is_string($this->value) && $this->value !== '') {
            return true;
        }
        return false;
    }

    /**
     * @return Operator|null
     */
    public function operator(): ?Operator
    {
        return $this->operator;
    }

    /**
     * @param NodeCollection $collection
     */
    public function updateCollection(NodeCollection $collection): void
    {
        $this->children = $collection;
    }

    /**
     * @return int
     */
    public function level(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return void
     */
    public function updateLevel(int $level): void
    {
        $this->level = $level;

        $subLevel = $level + 1;
        foreach ($this->children as $child) {
            $child->updateLevel($subLevel);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $configuration = Configuration::getInstance();
        $indentation = str_repeat($configuration->oneLevelIndentationString(), $this->level);
        $string = '';

        if ($this->operator instanceof Operator) {
            if ($this->operator->isMultiLine()) {
                $string .= $indentation . $this->identifier . ' (' . PHP_EOL . $this->value . PHP_EOL . ')' . PHP_EOL;
            } else {
                $string .= $indentation . trim($this->identifier . ' ' . $this->operator . ' ' . $this->value) . PHP_EOL;
            }
        }

        if ($this->children->count() > 0) {
            $string .= $indentation . $this->identifier . ' {' . PHP_EOL;
            $string .= $this->children;
            $string .= $indentation . '}' . PHP_EOL;
        }

        return $string;
    }
}
