<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Adapter\Configuration\Reader;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\DefaultConfigurationReader;

/**
 * Class DefaultConfigurationReaderTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Adapter\Configuration\Reader
 * @covers \Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\DefaultConfigurationReader
 */
final class DefaultConfigurationReaderTest extends TestCase
{
    /**
     * @var array
     */
    private $config = [
        'sniffs' => [
            0 => [
                'class'      => 'Indentation',
                'parameters' => [
                    'useSpaces'        => true,
                    'indentPerLevel'   => 2,
                    'indentConditions' => true,
                ],
            ],
            1 => [
                'class' => 'RepeatingRValue'
            ],
            2 => [
                'class' => 'DeadCode',
            ],
            3 => [
                'class' => 'OperatorWhitespace',
            ],
            4 => [
                'class' => 'DuplicateAssignment',
            ],
            5 => [
                'class' => 'EmptySection',
            ],
            6 => [
                'class'      => 'NestingConsistency',
                'parameters' => [
                    'commonPathPrefixThreshold' => 1,
                ],
            ],
        ],
        'paths'        => [],
        'filePatterns' => [],
    ];

    /**
     * @var DefaultConfigurationReader
     */
    private $defaultConfigurationReader;

    protected function setUp(): void
    {
        $this->defaultConfigurationReader = new DefaultConfigurationReader();
    }

    /**
     * @test
     */
    public function defaultConfigurationIsUSed(): void
    {
        $this->assertSame($this->config, $this->defaultConfigurationReader->getArrayCopy());
    }
}
