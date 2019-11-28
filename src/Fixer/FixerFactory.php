<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer;

use Pluswerk\TypoScriptAutoFixer\Exception\FixerNotFoundException;
use Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\IndentationFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\OperatorWhitespaceFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

final class FixerFactory
{
    /**
     * @param AbstractIssue $issue
     *
     * @return FixerInterface|null
     */
    public function getFixerByIssue(AbstractIssue $issue): ?FixerInterface
    {
        switch (get_class($issue)) {
            case OperatorWhitespaceIssue::class:
                return new OperatorWhitespaceFixer();
                break;
            case IndentationIssue::class:
                return new IndentationFixer();
        }
        throw new FixerNotFoundException('Fixer for issue ' . get_class($issue) . ' not found');
    }
}
