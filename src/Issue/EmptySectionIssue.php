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
    public function __construct(int $line, array $tokens)
    {
        parent::__construct($line);

        $lineGrouper = new LineGrouper($tokens);
        $tokenLines = $lineGrouper->getLines();

        $amountOfLines = count($tokenLines);
        for ($i = $line; $i<=$amountOfLines; $i++) {
            foreach ($tokenLines[$i] as $token) {
                if ($token->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                    $this->endLine = $i;
                    break 2;
                }
            }
        }
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
