<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

final class Identifier implements \Iterator, \Countable
{
    /**
     * @var array
     */
    private $items;

    /**
     * @var Identifier
     */
    private $reverseItems = null;

    /**
     * Identifier constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->items = explode('.', $identifier);
    }

    /**
     * @return string
     */
    public function current(): string
    {
        return current($this->items);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return key($this->items);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Identifier
     */
    public function reverseItems(): Identifier
    {
        if (!($this->reverseItems instanceof Identifier)) {
            $this->reverseItems = new Identifier(implode('.', array_reverse($this->items)));
        }
        return $this->reverseItems;
    }
}
