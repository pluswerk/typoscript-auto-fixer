<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Adapter\Configuration\Reader;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Exception\FailedReadConfigurationException;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\YamlConfigurationReader;

/**
 * Class YamlConfigurationReaderTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Adapter\Configuration\Reader
 * @covers \Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\YamlConfigurationReader
 */
final class YamlConfigurationReaderTest extends TestCase
{
    /**
     * @test
     * @dataProvider configurationFileProvider
     */
    public function configurationIsReadFromALinterConfigYamlFileByDefault($filePath, $expectedConfig): void
    {
        $fileBasePath = getcwd();

        copy($filePath, $fileBasePath . '/typoscript-lint.yml');

        $yamlConfigurationReader = new YamlConfigurationReader();
        $this->assertSame($expectedConfig, $yamlConfigurationReader->getArrayCopy());

        unlink($fileBasePath . '/typoscript-lint.yml');
    }

    /**
     * @test
     */
    public function anExceptionIsThrownIfFileDoesNotExist(): void
    {
        $this->expectException(FailedReadConfigurationException::class);
        new YamlConfigurationReader();
    }

    /**
     * @test
     * @dataProvider configurationFileProvider
     */
    public function aFilePathCanBeGivenToReadTheConfigurationFrom($filePath, $expectedConfig): void
    {
        $yamlConfigurationReader = new YamlConfigurationReader($filePath);
        $this->assertSame($expectedConfig, $yamlConfigurationReader->getArrayCopy());
    }

    public function configurationFileProvider()
    {
        return [
            'empty file' => [
                'filePath' => __DIR__ . '/Fixtures/empty-typoscript-lint.yml',
                'expectedConfig' => []
            ],
            'not empty file' => [
                'filePath' => __DIR__ . '/Fixtures/typoscript-lint.yml',
                'expectedConfig' => [
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
                ]
            ]
        ];
    }
}
