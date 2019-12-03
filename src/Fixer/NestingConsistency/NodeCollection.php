<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

class NodeCollection implements \Iterator, \Countable
{
    /**
     * @var Node[]
     */
    private $nodes = [];

    /**
     * @param Node $node
     */
    public function add(Node $node): void
    {
        if ($this->hasNode($node->identifier())) {
            $this->removeNode($node->identifier());
        }
        $this->nodes[] = $node;
    }

    /**
     * Return the current element
     * @link  https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(): Node
    {
        return current($this->nodes);
    }

    /**
     * Move forward to next element
     * @link  https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->nodes);
    }

    /**
     * Return the key of the current element
     * @link  https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): int
    {
        return key($this->nodes);
    }

    /**
     * Checks if current position is valid
     * @link  https://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return key($this->nodes) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link  https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->nodes);
    }

    /**
     * Count elements of an object
     * @link  https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return count($this->nodes);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function hasNode(string $string): bool
    {
        foreach ($this->nodes as $node) {
            if ($node->identifier() === $string) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $string
     *
     * @return Node|null
     */
    public function getNode(string $string): ?Node
    {
        foreach ($this->nodes as $node) {
            if ($node->identifier() === $string) {
                return $node;
            }
        }
        return null;
    }

    /**
     * @param string $title
     */
    private function removeNode(string $title): void
    {
        foreach ($this->nodes as $key => $node) {
            if ($node->identifier() === $title) {
                unset($this->nodes[$key]);
                $this->nodes = array_values($this->nodes);
            }
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = '';

        foreach ($this->nodes as $node) {
            $string .= $node;
        }

        return $string;
    }
}
