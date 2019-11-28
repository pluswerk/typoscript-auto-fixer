<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer;

use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\OperatorWhitespaceFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;

final class FixerFactory
{
    /**
     * @param AbstractIssue $issue
     *
     * @return FixerInterface|null
     */
    public function getFixerByIssue(AbstractIssue $issue): ?FixerInterface
    {
        return new OperatorWhitespaceFixer();
    }
}
