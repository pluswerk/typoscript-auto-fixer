<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

abstract class AbstractIssue
{
    /**
     * @var int
     */
    private $line;

    /**
     * AbstractIssue constructor.
     *
     * @param int $line
     */
    public function __construct(int $line)
    {
        $this->line = $line;
    }

    /**
     * @return int
     */
    public function line(): int
    {
        return $this->line;
    }
}
