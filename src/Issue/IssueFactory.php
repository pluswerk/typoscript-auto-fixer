<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Helmich\TypoScriptLint\Linter\Report\Issue;
use Pluswerk\TypoScriptAutoFixer\Exception\InvalidIssueException;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

final class IssueFactory
{
    /**
     * @param Issue $issue
     *
     * @return AbstractIssue|null
     */
    public function getIssue(Issue $issue): ?AbstractIssue
    {
        switch ($issue->getMessage()) {
            case 'Accessor should be followed by single space.':
            case 'No whitespace after object accessor.':
            case 'No whitespace after operator.':
            case 'Operator should be followed by single space.':
                return new OperatorWhitespaceIssue((int) $issue->getLine());
                break;
            default:
                return null;
                break;
        }
    }
}
