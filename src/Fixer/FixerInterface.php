<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer;

use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;

interface FixerInterface
{
    /**
     * @param File          $file
     * @param AbstractIssue $issue
     *
     * @return File
     */
    public function fixIssue(File $file, AbstractIssue $issue): File;
}
