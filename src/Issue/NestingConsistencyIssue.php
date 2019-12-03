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

    public function __construct(int $line, int $secondLine, array $tokens)
    {
        parent::__construct($line);

        $this->secondLine = $secondLine;

        $lineGrouper = new LineGrouper($tokens);
        $tokenLines = $lineGrouper->getLines();

        $amountOfLines = count($tokenLines);
        $openedBraces = 0;
        for ($i = $line; $i<=$amountOfLines; $i++) {
            foreach ($tokenLines[$i] as $token) {
                if ($token->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $openedBraces++;
                } elseif ($token->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                    $openedBraces--;
                }
            }
            if ($openedBraces === 0) {
                $this->firstEndLine = $i;
                break;
            }
        }

        $openedBraces = 0;
        for ($i = $secondLine; $i<=$amountOfLines; $i++) {
            foreach ($tokenLines[$i] as $token) {
                if ($token->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $openedBraces++;
                } elseif ($token->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                    $openedBraces--;
                }
            }
            if ($openedBraces === 0) {
                $this->secondEndLine = $i;
                break;
            }
        }
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
