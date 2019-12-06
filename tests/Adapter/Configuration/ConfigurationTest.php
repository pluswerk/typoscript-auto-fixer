<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Adapter\Configuration;

use Helmich\TypoScriptLint\Linter\LinterConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\AbstractConfigurationReader;
use Pluswerk\TypoScriptAutoFixer\Exception\ConfigurationInstantiationException;

/**
 * Class ConfigurationTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Adapter\Configuration
 * @covers \Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration
 */
final class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

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
                'class' => 'DeadCode',
            ],
            2 => [
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

    protected function setUp(): void
    {
        Configuration::destroyInstance();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Configuration::destroyInstance();
    }

    /**
     * @test
     * @dataProvider configurationProvider
     */
    public function linterConfigurationCanBeBuilt($linterConfiguration): void
    {
        $configuration = Configuration::getInstance();
        $this->assertEquals($linterConfiguration, $configuration->getLinterConfiguration());
    }

    /**
     * @test
     */
    public function instantiationThrowsExceptionWithoutInitialisationAfterFirstInstantiation(): void
    {
        $configuration = Configuration::getInstance();
        $this->expectException(ConfigurationInstantiationException::class);
        Configuration::getInstance();
    }

    /**
     * @test
     */
    public function ifInstanceWasInitialisedAnInstanceIsReturned(): void
    {
        $configuration = Configuration::getInstance();
        $configuration->init();
        $secondConfiguration = Configuration::getInstance();
        $this->assertSame($configuration, $secondConfiguration);
    }

    /**
     * @test
     */
    public function initialisationSetsConfigurationReader(): void
    {
        /** @var AbstractConfigurationReader|MockObject $reader */
        $reader = $this->createMock(AbstractConfigurationReader::class);
        $configuration = Configuration::getInstance();

        $reader->expects($this->once())->method('getArrayCopy');

        $configuration->init($reader);
    }

    /**
     * @test
     * @dataProvider indentationStringProvider
     */
    public function oneLevelIndentationStringCanBeFetched($indentationString, $config): void
    {
        /** @var AbstractConfigurationReader|MockObject $reader */
        $reader = $this->createMock(AbstractConfigurationReader::class);
        $configuration = Configuration::getInstance();

        $reader->expects($this->once())->method('getArrayCopy')->willReturn($config);

        $configuration->init($reader);

        $this->assertSame($indentationString, $configuration->oneLevelIndentationString());
    }

    public function configurationProvider()
    {
        $config = [
            'sniffs' => [
                'Indentation' => [
                        'parameters' => [
                            'useSpaces' => true,
                            'indentPerLevel' => 2,
                            'indentConditions' => true,
                        ],
                        'disabled' => false,
                    ],
                'DeadCode' => [
                    'disabled' => false,
                ],
                'OperatorWhitespace' => [
                    'disabled' => false,
                ],
                'DuplicateAssignment' => [
                    'disabled' => false,
                ],
                'EmptySection' => [
                    'disabled' => false,
                ],
                'NestingConsistency' => [
                    'parameters' => [
                        'commonPathPrefixThreshold' => 1,
                    ],
                    'disabled' => false,
                ],
            ],
            'paths' => [],
            'filePatterns' => [],
        ];
        $linterconfiguration = new LinterConfiguration();
        $linterconfiguration->setConfiguration($config);

        return [
            [
                'linterConfiguration' => $linterconfiguration
            ]
        ];
    }

    public function indentationStringProvider(): array
    {
        $configA = $this->config;
        $configB = $this->config;
        $configB['sniffs'][0]['parameters']['useSpaces'] = false;
        $configC = $configB;
        $configC['sniffs'][0]['parameters']['indentPerLevel'] = 1;

        return [
            [
                'indentationString' => '  ',
                'config' => $configA
            ],
            [
                'indentationString' => "\t\t",
                'config' => $configB
            ],
            [
                'indentationString' => "\t",
                'config' => $configC
            ]
        ];
    }
}
