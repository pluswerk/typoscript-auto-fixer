<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\EmptySection;

use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\Fixer\AbstractFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue;

final class EmptySectionFixer extends AbstractFixer
{

    /**
     * @param File          $file
     * @param AbstractIssue|EmptySectionIssue $issue
     *
     * @return File
     */
    public function fixIssue(File $file, AbstractIssue $issue): File
    {
        $lines = range($issue->startLine(), $issue->endLine());
        $file->removeLines($lines);
        $file = $this->fileBuilder->buildFile($file->getPathname());
        return $file;
    }
}
