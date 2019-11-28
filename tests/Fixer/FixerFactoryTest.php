<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\FixerFactory;
use Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace\OperatorWhitespaceFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\OperatorWhitespaceIssue;

final class FixerFactoryTest extends TestCase
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
     */
    public function forAIssueTheCorrectFixerIsCreated(): void
    {
        $issue = new OperatorWhitespaceIssue(13);
        $fixer = $this->fixerFactory->getFixerByIssue($issue);
        $this->assertInstanceOf(OperatorWhitespaceFixer::class, $fixer);
    }
}
