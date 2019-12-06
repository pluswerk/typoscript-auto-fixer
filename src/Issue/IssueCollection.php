<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;

class IssueCollection implements \Countable, \Iterator
{
    /**
     * @var AbstractIssue[]
     */
    private $issues = [];

    /**
     * @param AbstractIssue $issue
     */
    public function add(AbstractIssue $issue): void
    {
        $this->issues[] = $issue;
        usort($this->issues, static function ($a, $b) {
            /** @var AbstractIssue $a */
            /** @var AbstractIssue $b */
            if ($a->line() > $b->line()) {
                return 1;
            }
            if ($a->line() < $b->line()) {
                return -1;
            }
            if ($a->line() === $b->line()) {
                return 0;
            }
        });
    }

    /**
     * @return AbstractIssue
     */
    public function current(): AbstractIssue
    {
        return current($this->issues);
    }

    /**
     * @return void
     */
    public function next(): void
    {
        next($this->issues);
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return key($this->issues);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->issues) !== null;
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        reset($this->issues);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->issues);
    }
}
