<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Adapter;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptParser\Tokenizer\LineGrouper;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

final class IssueFactory
{
    private const INDENTATION_PATTERN = '/^Expected indent of (\d{1,2}) (spaces?|tabs?)\.$/';
    private const NESTED_CONSISTENCY_01 = '/^Common path prefix \".*\" with assignment to \".*\" in line (\d{1,})\. Consider merging them into a nested assignment.$/';
    private const NESTED_CONSISTENCY_02 = '/^Assignment to value \".*\", altough nested statement for path \".*\" exists at line (\d{1,})\.$/';
    private const NESTED_CONSISTENCY_03 = '/^Multiple nested statements for object path \"(.*)\"\. Consider merging them into one statement\.$/';

    /**
     * @var AdapterUtility
     */
    private $adapterUtility;

    public function __construct()
    {
        $this->adapterUtility = new AdapterUtility();
    }

    /**
     * @param Issue $issue
     * @param array $tokens
     *
     * @return AbstractIssue|null
     */
    public function getIssue(Issue $issue, array $tokens): ?AbstractIssue
    {
        $message = $issue->getMessage();
        $tokenLines = new LineGrouper($tokens);

        switch ($message) {
            // OperatorWhitespaceIssue
            case 'Accessor should be followed by single space.':
            case 'No whitespace after object accessor.':
            case 'No whitespace after operator.':
            case 'Operator should be followed by single space.':
                return new OperatorWhitespaceIssue((int) $issue->getLine());
                break;

            // IndentationIssue
            case (preg_match(self::INDENTATION_PATTERN, $issue->getMessage(), $matches) ? $message : !$message):
                return $this->buildIndentationIssue($issue, $matches);
                break;

            // EmptySectionIssue
            case 'Empty assignment block':
                return $this->buildEmptySectionIssue($issue, $tokens);
                break;

            // NestingConsistencyIssue
            case (preg_match(self::NESTED_CONSISTENCY_01, $issue->getMessage(), $matches) ? $message : !$message):
            case (preg_match(self::NESTED_CONSISTENCY_02, $issue->getMessage(), $matches) ? $message : !$message):
                return $this->buildNestingConsistencyIssue0102($issue, $matches, $tokens);
                break;
            case (preg_match(self::NESTED_CONSISTENCY_03, $issue->getMessage(), $matches) ? $message : !$message):
                return $this->buildNestingConsistencyIssue03($issue, $matches, $tokens);
                break;

            // Default is null
            default:
                return null;
                break;
        }
    }

    /**
     * @param Issue $issue
     * @param array $tokens
     *
     * @return EmptySectionIssue|null
     */
    private function buildEmptySectionIssue(Issue $issue, array $tokens): ?EmptySectionIssue
    {
        $tokenLines = new LineGrouper($tokens);
        $startLine = (int) $issue->getLine();
        $lines = $tokenLines->getLines();
        $amountOfLines = count($lines);
        for ($i = $startLine; $i<=$amountOfLines; $i++) {
            foreach ($lines[$i] as $token) {
                if ($token->getType() === TokenInterface::TYPE_BRACE_CLOSE) {
                    $endLine = $i;
                    return new EmptySectionIssue($startLine, $endLine);
                    break 2;
                }
            }
        }
        return null;
    }

    /**
     * @param Issue $issue
     * @param array $matches
     *
     * @return IndentationIssue
     */
    private function buildIndentationIssue(Issue $issue, array $matches): IndentationIssue
    {
        $char = ' ';
        if ($matches[2] === 'tab' || $matches[2] === 'tabs') {
            $char = "\t";
        }
        return new IndentationIssue((int) $issue->getLine(), (int)($matches[1] ?? 0), $char);
    }

    /**
     * @param Issue $issue
     * @param array $matches
     * @param array $tokens
     *
     * @return NestingConsistencyIssue
     */
    private function buildNestingConsistencyIssue0102(Issue $issue, array $matches, array $tokens): NestingConsistencyIssue
    {
        $secondLine = (int) $matches[1] ?? 0;
        $firstLine = (int) $issue->getLine();

        if ($secondLine < $firstLine) {
            [$secondLine, $firstLine] = [$firstLine, $secondLine];
        }

        $firstEndLine = $this->adapterUtility->findEndLineOfNestedStatement($firstLine, $tokens);
        $secondEndLine = $this->adapterUtility->findEndLineOfNestedStatement($secondLine, $tokens);

        if ($secondLine === 0 || $firstEndLine === 0 || $secondEndLine === 0) {
            return null;
        }

        return new NestingConsistencyIssue($firstLine, $secondLine, $firstEndLine, $secondEndLine);
    }

    /**
     * @param Issue $issue
     * @param       $matches
     * @param array $tokens
     *
     * @return NestingConsistencyIssue|null
     */
    private function buildNestingConsistencyIssue03(Issue $issue, $matches, array $tokens): ?NestingConsistencyIssue
    {
        $secondLine = (int) $issue->getLine();
        $firstLine = $this->adapterUtility->findFirstNestedAppearanceOfObjectPath($secondLine, $matches[1], $tokens);
        $firstEndLine = $this->adapterUtility->findEndLineOfNestedStatement($firstLine, $tokens);
        $secondEndLine = $this->adapterUtility->findEndLineOfNestedStatement($secondLine, $tokens);

        if ($firstLine === 0 || $firstEndLine === 0 || $secondEndLine === 0) {
            return null;
        }

        return new NestingConsistencyIssue($firstLine, $secondLine, $firstEndLine, $secondEndLine);
    }
}
