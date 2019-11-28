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
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->commandTester = null;
    }

    /**
     * @test
     */
    public function commandFixesFile(): void
    {
        $expectedFile = __DIR__ . '/Fixtures/expected.typoscript';
        $filePathName = __DIR__ . '/Fixtures/test.typoscript';
        $filePathToFix = __DIR__ . '/Fixtures/tmp_test.typoscript';
        if (is_file($filePathToFix)) {
            unlink($filePathToFix);
        }
        copy($filePathName, $filePathToFix);
        $this->commandTester->execute(['files' => [$filePathToFix]]);
        $this->assertFileEquals($expectedFile, $filePathToFix);
        unlink($filePathToFix);
    }
}
