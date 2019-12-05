<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Adapter;

use Helmich\TypoScriptParser\Tokenizer\LineGrouper;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

final class AdapterUtility
{
    /**
     * @param int   $startLine
     * @param array $tokens
     *
     * @return int
     */
    public function findEndLineOfNestedStatement(int $startLine, array $tokens): int
    {
        $endLine = 0;
        $tokenLines = new LineGrouper($tokens);
        $lines = $tokenLines->getLines();

        $lines = array_slice($lines, $startLine - 1);

        $openedBraces = 0;
        /** @var TokenInterface[] $line */
        foreach ($lines as $line) {
            foreach ($line as $token) {
                if ($token->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                    $openedBraces++;
                }
                if ($token->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                    $openedBraces--;
                }
            }
            if ($openedBraces === 0) {
                $endLine = $line[0]->getLine();
                break;
            }
        }

        return $endLine;
    }

    /**
     * @param int    $startLine
     * @param string $objectPath
     * @param array  $tokens
     *
     * @return int
     */
    public function findFirstNestedAppearanceOfObjectPath(int $startLine, string $objectPath, array $tokens): int
    {
        $tokenLines = new LineGrouper($tokens);
        $lines = $tokenLines->getLines();

        /** @var TokenInterface[] $line */
        foreach ($lines as $line) {
            $value = $line[0]->getValue();
            if (preg_match('/^' . $objectPath . '($|\..*)/', $value) && $line[0]->getLine() !== $startLine) {
                foreach ($line as $token) {
                    if ($token->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                        return $line[0]->getLine();
                    }
                }
            }
        }

        return 0;
    }
}
