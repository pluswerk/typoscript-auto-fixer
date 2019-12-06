<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration;
use Pluswerk\TypoScriptAutoFixer\Exception\ConfigurationInstantiationException;
use Pluswerk\TypoScriptAutoFixer\Exception\FixerNotFoundException;
use Pluswerk\TypoScriptAutoFixer\Fixer\EmptySection\EmptySectionFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\NestingConsistencyFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\FixerFactory;
use Pluswerk\TypoScriptAutoFixer\Fixer\Indentation\IndentationFixer;
use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\OperatorWhitespaceFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\EmptySectionIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IndentationIssue;

final class DummyIssue extends AbstractIssue
{
}

/**
 * Class FixerFactoryTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\FixerFactory
 */
final class FixerFactoryTest extends TestCase //phpcs:ignore
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    protected function setUp(): void
    {
        $this->fixerFactory = new FixerFactory();
    }

    /**
     * @test
     * @dataProvider issueProvider
     */
    public function forAIssueTheCorrectFixerIsCreated($issue, $expeted): void
    {
        try {
            $config = Configuration::getInstance();
            $config->init();
        } catch (ConfigurationInstantiationException $e) {
            Configuration::destroyInstance();
            $config = Configuration::getInstance();
            $config->init();
        }
        $fixer = $this->fixerFactory->getFixerByIssue($issue);
        $this->assertInstanceOf($expeted, $fixer);
    }

    public function issueProvider()
    {
        return [
            OperatorWhitespaceIssue::class => [
                'issue' => new OperatorWhitespaceIssue(13),
                'expected' => OperatorWhitespaceFixer::class
            ],
            IndentationIssue::class => [
                'issue' => new IndentationIssue(13, 4),
                'expected' => IndentationFixer::class
            ],
            EmptySectionIssue::class => [
                'issue' => new EmptySectionIssue(13, 15),
                'expected' => EmptySectionFixer::class
            ],
            NestingConsistencyIssue::class => [
                'issue' => new NestingConsistencyIssue(13, 17, 15, 19),
                'expected' => NestingConsistencyFixer::class
            ]
        ];
    }

    /**
     * @test
     */
    public function ifNoFixerFoundAnExceptionIsThrown(): void
    {
        $issue = new DummyIssue(1);
        $this->expectException(FixerNotFoundException::class);
        $this->fixerFactory->getFixerByIssue($issue);
    }
}
