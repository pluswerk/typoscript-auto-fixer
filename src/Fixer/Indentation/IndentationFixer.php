<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\Indentation;

use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\AbstractFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;

final class IndentationFixer extends AbstractFixer
{
    /**
     * @var LineFixer
     */
    private $lineFixer;

    /**
     * IndentationFixer constructor.
     *
     * @param FileBuilder|null $fileBuilder
     * @param LineFixer|null   $lineFixer
     */
    public function __construct(FileBuilder $fileBuilder = null, LineFixer $lineFixer = null)
    {
        parent::__construct($fileBuilder);
        $this->lineFixer = $lineFixer ?? new LineFixer();
    }

    /**
     * @param File          $file
     * @param AbstractIssue|IndentationIssue $issue
     *
     * @return File
     */
    public function fixIssue(File $file, AbstractIssue $issue): File
    {
        $line = $file->readLine($issue->line());
        $line = $this->lineFixer->fixIndentation($line, $issue->amountOfIndentChars(), $issue->indentationCharacter());
        $file->replaceLine($line, $issue->line());
        return $this->fileBuilder->buildFile($file->getPathname());
    }
}
