<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Command;

use Exception;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\GrumphpConfigurationReader;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\YamlConfigurationReader;
use Pluswerk\TypoScriptAutoFixer\Fixer\IssueFixer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class FixCommand extends Command
{
    /**
     * @var IssueFixer
     */
    private IssueFixer $issueFixer;

    /**
     * @var array
     */
    private array $filesList = [];

    public function __construct(string $name = null)
    {
        $name = $name ?? 'fix';
        parent::__construct($name);
        $this->issueFixer = new IssueFixer();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('files', InputArgument::IS_ARRAY, 'files to fix', []);
        $this->addOption(
            'typoscript-linter-configuration',
            't',
            InputOption::VALUE_NONE,
            'if set the configuration file style is the typoscript-lint.yml file style'
        );
        $this->addOption(
            'grumphp-configuration',
            'g',
            InputOption::VALUE_NONE,
            'if set the configuration file style is the grumphp.yml file style'
        );
        $this->addOption(
            'configuration-file',
            'c',
            InputOption::VALUE_REQUIRED,
            'if set the configuration file of given path is used!',
            ''
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->initConfiguration($input);
        $filesArguments = $input->getArgument('files');

        if (count($filesArguments) > 0) {
            foreach ($filesArguments as $fileArgument) {
                if ($this->isTypoScript($fileArgument)) {
                    $this->filesList[] = $fileArgument;
                } elseif (is_dir($fileArgument)) {
                    $dirContents = $this->getDirContents($fileArgument);
                    foreach($dirContents as $dirContent) {
                        if ($this->isTypoScript($dirContent)) {
                            $this->filesList[] = $dirContent;
                        }
                    }
                }
            }

            if (count($this->filesList) > 0) {
                $table = new Table($output);

                $count = 1;
                foreach ($this->filesList as $file) {
                    $table->addRow([
                        sprintf('# %s', $count),
                        $file
                    ]);
                    $this->issueFixer->fixIssuesForFile($file, $output);
                    $count++;
                }
                $output->writeln('');
                $output->writeln('Checked files:');
                $table->render();
            } else {
                $output->writeln('No TypoScript files found');
            }
        } else {
            $output->writeln('Path and files not specified');
        }
        return 0;
    }

    /**
     * @param $dir
     * @param array $results
     * @return array
     */
    private function getDirContents($dir, array &$results = []): array
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value !== '.' && $value !== '..') {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * @param $file
     * @return bool
     */
    private function isTypoScript($file): bool
    {
        return is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'typoscript';
    }

    /**
     * @param InputInterface $input
     */
    private function initConfiguration(InputInterface $input): void
    {
        $configuration = Configuration::getInstance();
        $configReader = null;

        if ($input->getOption('typoscript-linter-configuration')) {
            $configReader = new YamlConfigurationReader();
        } elseif ($input->getOption('grumphp-configuration')) {
            $configReader = new GrumphpConfigurationReader();
        }

        $configurationFile = $input->getOption('configuration-file');

        if ($configurationFile !== '' && $input->getOption('typoscript-linter-configuration')) {
            $configReader = new YamlConfigurationReader($configurationFile);
        }

        if ($configurationFile !== '' && $input->getOption('grumphp-configuration')) {
            $configReader = new GrumphpConfigurationReader($configurationFile);
        }

        $configuration->init($configReader);
    }
}
