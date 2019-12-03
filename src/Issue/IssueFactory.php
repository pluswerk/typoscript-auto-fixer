<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use Helmich\TypoScriptParser\Tokenizer\LineGrouper;
use Helmich\TypoScriptParser\Tokenizer\TokenInterface;

final class IssueFactory
{
    private const INDENTATION_PATTERN = '/^Expected indent of (\d{1,2}) (spaces?|tabs?)\.$/';
    private const NESTED_CONSISTENCY_01 = '/^Common path prefix \".*\" with assignment to \".*\" in line (\d{1,})\. Consider merging them into a nested assignment.$/';
    private const NESTED_CONSISTENCY_02 = '/^Assignment to value \".*\", altough nested statement for path \".*\" exists at line (\d{1,})\.$/';
    private const NESTED_CONSISTENCY_03 = '/^Multiple nested statements for object path \"(.*)\"\. Consider merging them into one statement\.$/';

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
            case 'Accessor should be followed by single space.':
            case 'No whitespace after object accessor.':
            case 'No whitespace after operator.':
            case 'Operator should be followed by single space.':
                return new OperatorWhitespaceIssue((int) $issue->getLine());
                break;
            case (preg_match(self::INDENTATION_PATTERN, $issue->getMessage(), $matches) ? $message : !$message):
                $char = ' ';
                if ($matches[2] === 'tab' || $matches[2] === 'tabs') {
                    $char = "\t";
                }
                return new IndentationIssue((int) $issue->getLine(), (int)($matches[1] ?? 0), $char);
                break;
            case 'Empty assignment block':
                return new EmptySectionIssue((int) $issue->getLine(), $tokens);
                break;
            case (preg_match(self::NESTED_CONSISTENCY_01, $issue->getMessage(), $matches) ? $message : !$message):
            case (preg_match(self::NESTED_CONSISTENCY_02, $issue->getMessage(), $matches) ? $message : !$message):
                $secondLine = $matches[1] ?? 0;
                $firstLine = (int) $issue->getLine();
                if ((int) $issue->getLine() > $secondLine) {
                    $firstLine = $secondLine;
                    $secondLine = (int) $issue->getLine();
                }
                return new NestingConsistencyIssue((int) $firstLine, (int) $secondLine, $tokens);
                break;
            case (preg_match(self::NESTED_CONSISTENCY_03, $issue->getMessage(), $matches) ? $message : !$message):
                $secondLine = (int) $issue->getLine();
                foreach ($tokenLines->getLines() as $key => $tokenLine) {
                    $value = $tokenLine[0]->getValue();
                    if (preg_match('/^' . $matches[1] . '($|\..*)/', $value)) {
                        foreach ($tokenLine as $token) {
                            if ($token->getType() === TokenInterface::TYPE_BRACE_OPEN) {
                                $firstLine = $tokenLine[0]->getLine();
                                return new NestingConsistencyIssue($firstLine, $secondLine, $tokens);
                            }
                        }
                    }
                }
                return null;
                break;
            default:
                return null;
                break;
        }
    }
}
