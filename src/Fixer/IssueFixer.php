<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer;

use Pluswerk\TypoScriptAutoFixer\FileBuilder;

final class IssueFixer
{
    /**
     * @var FileBuilder
     */
    private $fileBuilder;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    public function __construct(FileBuilder $fileBuilder = null, FixerFactory $fixerFactory = null)
    {
        $this->fileBuilder = $fileBuilder ?? new FileBuilder();
        $this->fixerFactory = $fixerFactory ?? new FixerFactory();
    }

    /**
     * @param string $filePath
     */
    public function fixIssuesForFile(string $filePath): void
    {
        $file = $this->fileBuilder->buildFile($filePath);
        $count = 0;
        while ($file->issues()->count() > 0 && $count < 50) {
            $issue = $file->issues()->current();
            $fixer = $this->fixerFactory->getFixerByIssue($issue);
            $file = $fixer->fixIssue($file, $issue);
            $count++;
        }
    }
}
