<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Test\Adapter\Configuration\Reader;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\GrumphpConfigurationReader;
use Pluswerk\TypoScriptAutoFixer\Exception\FailedReadConfigurationException;

/**
 * Class GrumphpConfigurationReaderTest
 * @package Pluswerk\TypoScriptAutoFixer\Test\Adapter\Configuration\Reader
 * @covers \Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\GrumphpConfigurationReader
 */
final class GrumphpConfigurationReaderTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $fileBasePath = getcwd();

        if (file_exists($fileBasePath . '/original-grumphp.yml')) {
            rename($fileBasePath . '/original-grumphp.yml', $fileBasePath . '/grumphp.yml');
        }
    }

    /**
     * @test
     * @dataProvider configurationFileProvider
     */
    public function configurationIsReadFromAGrumphpConfigYamlFile($filePath, $expectedConfig): void
    {
        $fileBasePath = getcwd();

        if (file_exists($fileBasePath . '/grumphp.yml')) {
            rename($fileBasePath . '/grumphp.yml', $fileBasePath . '/original-grumphp.yml');
        }

        copy($filePath, $fileBasePath . '/grumphp.yml');

        $yamlConfigurationReader = new GrumphpConfigurationReader();
        $this->assertSame($expectedConfig, $yamlConfigurationReader->getArrayCopy());

        unlink($fileBasePath . '/grumphp.yml');

        if (file_exists($fileBasePath . '/original-grumphp.yml')) {
            rename($fileBasePath . '/original-grumphp.yml', $fileBasePath . '/grumphp.yml');
        }
    }

    /**
     * @test
     */
    public function anExceptionIsThrownIfFileDoesNotExist(): void
    {
        $fileBasePath = getcwd();

        if (file_exists($fileBasePath . '/grumphp.yml')) {
            rename($fileBasePath . '/grumphp.yml', $fileBasePath . '/original-grumphp.yml');
        }

        $this->expectException(FailedReadConfigurationException::class);
        new GrumphpConfigurationReader();
    }

    /**
     * @test
     * @dataProvider configurationFileProvider
     */
    public function aFilePathCanBeGivenToReadTheConfigurationFrom($filePath, $expectedConfig): void
    {
        $yamlConfigurationReader = new GrumphpConfigurationReader($filePath);
        $this->assertSame($expectedConfig, $yamlConfigurationReader->getArrayCopy());
    }

    public function configurationFileProvider()
    {
        return [
            'not cofigured task' => [
                'filePath' => __DIR__ . '/Fixtures/empty-grumphp.yml',
                'expectedConfig' => []
            ],
            'not empty file' => [
                'filePath' => __DIR__ . '/Fixtures/grumphp.yml',
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
