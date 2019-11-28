<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace;

use Pluswerk\TypoScriptAutoFixer\Exception\NoOperatorFoundException;
use Pluswerk\TypoScriptAutoFixer\Fixer\AbstractFixer;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\FixerInterface;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

final class OperatorWhitespaceFixer extends AbstractFixer
{
    /**
     * @var LineFixer|null
     */
    private $lineFixer;

    /**
     * OperatorWhitespaceFixer constructor.
     *
     * @param FileBuilder    $fileBuilder
     * @param LineFixer|null $lineFixer
     */
    public function __construct(FileBuilder $fileBuilder = null, LineFixer $lineFixer = null)
    {
        parent::__construct($fileBuilder);
        $this->lineFixer = $lineFixer ?? new LineFixer();
    }

    /**
     * @param File          $file
     * @param AbstractIssue $issue
     *
     * @return File
     */
    public function fixIssue(File $file, AbstractIssue $issue): File
    {
        $line = $file->readLine($issue->line());
        try {
            $line = $this->lineFixer->fixOperatorWhitespace($line);
        } catch (NoOperatorFoundException $e) {
            return $file;
        }
        $file->replaceLine($line, $issue->line());
        return $this->fileBuilder->buildFile($file->getPathname());
    }
}
