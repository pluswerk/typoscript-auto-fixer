<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer;

use Exception;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class IssueFixer
{
    /**
     * @var FileBuilder
     */
    private FileBuilder $fileBuilder;

    /**
     * @var FixerFactory
     */
    private FixerFactory $fixerFactory;

    public function __construct(FileBuilder $fileBuilder = null, FixerFactory $fixerFactory = null)
    {
        $this->fileBuilder = $fileBuilder ?? new FileBuilder();
        $this->fixerFactory = $fixerFactory ?? new FixerFactory();
    }

    /**
     * @param string $filePath
     * @param OutputInterface $output
     * @throws Exception
     */
    public function fixIssuesForFile(string $filePath, OutputInterface $output): void
    {
        $file = $this->fileBuilder->buildFile($filePath);

        if ($file->issues()->count() > 0) {
            ProgressBar::setFormatDefinition('file', '%current%/%max% [%bar%] %percent:3s%% File: %file%');
            $progressBar = new ProgressBar($output, $file->issues()->count());
            $progressBar->setFormat('file');
            $progressBar->start();

            while ($file->issues()->count() > 0) {
                $progressBar->setMessage($filePath, 'file');
                $issue = $file->issues()->current();
                $fixer = $this->fixerFactory->getFixerByIssue($issue);
                $file = $fixer->fixIssue($file, $issue);
                $progressBar->advance();
                usleep(1000);
            }
            $file->removeNeedlessEmptyLines();
            $progressBar->finish();
            $output->writeln('');
        }
    }
}
