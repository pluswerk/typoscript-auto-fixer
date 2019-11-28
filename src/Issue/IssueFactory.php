<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;

final class IssueFactory
{
    private const INDENTATION_PATTERN = '/^Expected indent of (\d{1,2}) (spaces?|tabs?)\.$/';

    /**
     * @param Issue $issue
     *
     * @return AbstractIssue|null
     */
    public function getIssue(Issue $issue): ?AbstractIssue
    {
        $message = $issue->getMessage();

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
            default:
                return null;
                break;
        }
    }
}
