<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Command;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Command\FixCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class FixCommandTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Command
 * @covers Pluswerk\TypoScriptAutoFixer\Command\FixCommand
 */
final class FixCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new FixCommand());
        $command = $application->find('fix');
        $this->commandTester = new CommandTester($command);

        $this->prepareConfigurationFiles();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->commandTester = null;

        $this->deleteAndRestoreClonfigfiles();
    }

    /**
     * @test
     * @dataProvider configurationFileProvider
     */
    public function commandFixesFile($expectedFile, $filePathName, $filePathToFix, $executeArray): void
    {
        if (is_file($filePathToFix)) {
            unlink($filePathToFix);
        }
        copy($filePathName, $filePathToFix);
        $this->commandTester->execute($executeArray);
        $this->assertFileEquals($expectedFile, $filePathToFix);
        unlink($filePathToFix);
    }

    public function configurationFileProvider(): array
    {
        $expectedFile = __DIR__ . '/Fixtures/expected.typoscript';
        $filePathName = __DIR__ . '/Fixtures/test.typoscript';
        $filePathToFix = __DIR__ . '/Fixtures/tmp_test.typoscript';

        return [
            'internal default configuration' => [
                'expectedFile' => $expectedFile,
                'filePathName' => $filePathName,
                'filePathToFix' => $filePathToFix,
                'executeArray' => ['files' => [$filePathToFix]]
            ],
            'typoscript lint configuration' => [
                'expectedFile' => __DIR__ . '/Fixtures/expected-typoscript-lint-configuration.typoscript',
                'filePathName' => __DIR__ . '/Fixtures/test-typoscript-lint-configuration.typoscript',
                'filePathToFix' => $filePathToFix,
                'executeArray' => ['-t' => true, 'files' => [$filePathToFix]]
            ],
            'different name of typoscript lint configuration' => [
                'expectedFile' => __DIR__ . '/Fixtures/expected-different-name-configuration.typoscript',
                'filePathName' => __DIR__ . '/Fixtures/test-different-name-configuration.typoscript',
                'filePathToFix' => $filePathToFix,
                'executeArray' => ['-t' => true, '-c' => 'different-name.yml', 'files' => [$filePathToFix]]
            ]
        ];
    }

    private function prepareConfigurationFiles()
    {
        $basePath = getcwd();
        $typoScriptLintFixtureConfigFile = __DIR__ . '/Fixtures/typoscript-lint.yml';
        $typoScriptLintFixtureConfigFileDifferentName = __DIR__ . '/Fixtures/different-name.yml';

        if (file_exists($basePath . '/typoscript-lint.yml')) {
            rename($basePath . '/typoscript-lint.yml', $basePath . '/original-typoscript-lint.yml');
        }

        copy($typoScriptLintFixtureConfigFile, $basePath . '/typoscript-lint.yml');
        copy($typoScriptLintFixtureConfigFileDifferentName, $basePath . '/different-name.yml');
    }

    private function deleteAndRestoreClonfigfiles()
    {
        $basePath = getcwd();

        unlink($basePath . '/typoscript-lint.yml');
        unlink($basePath . '/different-name.yml');

        if (file_exists($basePath . '/original-typoscript-lint.yml')) {
            rename($basePath . '/original-typoscript-lint.yml', $basePath . '/typoscript-lint.yml');
        }
    }
}
