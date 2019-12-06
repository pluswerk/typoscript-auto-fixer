<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Command;

use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration;
use Pluswerk\TypoScriptAutoFixer\Fixer\IssueFixer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixCommand extends Command
{
    /**
     * @var IssueFixer
     */
    private $issueFixer;

    public function __construct(string $name = null)
    {
        $name = $name ?? 'fix';
        parent::__construct($name);
        $this->issueFixer = new IssueFixer();
    }

    protected function configure()
    {
        $this->addArgument('files', InputArgument::IS_ARRAY, 'files to fix', []);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $configuration = Configuration::getInstance();
        $configuration->init();
        $files = $input->getArgument('files');
        if (count($files) > 0) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    $this->issueFixer->fixIssuesForFile($file);
                }
            }
        }
        return 0;
    }
}
