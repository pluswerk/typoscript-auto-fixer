<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Helmich\TypoScriptParser\Tokenizer\LineGrouper;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

final class EmptySectionIssue extends AbstractIssue
{
    /**
     * @var int
     */
    private $endLine;

    /**
     * EmptySectionIssue constructor.
     *
     * @param int   $line
     * @param TokenInterface[] $tokens
     */
    public function __construct(int $line, int $endLine)
    {
        parent::__construct($line);

        $this->endLine = $endLine;
    }

    /**
     * @return int
     */
    public function startLine(): int
    {
        return $this->line();
    }

    public function endLine()
    {
        return $this->endLine;
    }
}
