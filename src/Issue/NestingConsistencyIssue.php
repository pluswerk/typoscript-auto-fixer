<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Helmich\TypoScriptParser\Tokenizer\LineGrouper;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

final class NestingConsistencyIssue extends AbstractIssue
{
    /**
     * @var int
     */
    private $secondLine;

    /**
     * @var int
     */
    private $firstEndLine;

    /**
     * @var int
     */
    private $secondEndLine;

    public function __construct(int $line, int $secondLine, int $firstEndLine, int $secondEndLine)
    {
        parent::__construct($line);

        $this->firstEndLine = $firstEndLine;
        $this->secondLine = $secondLine;
        $this->secondEndLine = $secondEndLine;
    }

    /**
     * @return int
     */
    public function secondLine(): int
    {
        return $this->secondLine;
    }

    /**
     * @return int
     */
    public function firstEndLine(): int
    {
        return $this->firstEndLine;
    }

    /**
     * @return int
     */
    public function secondEndLine(): int
    {
        return $this->secondEndLine;
    }
}
