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
     * @param ProgressBar $progressBarSub
     * @throws Exception
     */
    public function fixIssuesForFile(string $filePath, OutputInterface $output, ProgressBar $progressBarSub): void
    {
        $file = $this->fileBuilder->buildFile($filePath);

        if ($file->issues()->count() > 0) {
            $progressBarSub->setMaxSteps($file->issues()->count());
            $progressBarSub->start();

            while ($file->issues()->count() > 0) {
                $progressBarSub->setMessage($filePath, 'file');
                $issue = $file->issues()->current();
                $fixer = $this->fixerFactory->getFixerByIssue($issue);
                $file = $fixer->fixIssue($file, $issue);
                $progressBarSub->advance();
                usleep(1000);
            }
            $file->removeNeedlessEmptyLines();
            $progressBarSub->finish();
            $output->writeln('');
        }
    }
}
