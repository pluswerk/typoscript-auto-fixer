<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Adapter\Configuration;

use Helmich\TypoScriptLint\Linter\LinterConfiguration;
use Helmich\TypoScriptLint\Linter\Sniff\IndentationSniff;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\AbstractConfigurationReader;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader\DefaultConfigurationReader;
use Pluswerk\TypoScriptAutoFixer\Exception\ConfigurationInstantiationException;
use Symfony\Component\Config\Definition\Processor;

final class Configuration
{
    private static $instance;

    /**
     * @var LinterConfiguration
     */
    private $linterConfiguration;

    /**
     * @var bool
     */
    private $initNeeded = false;

    /**
     * @var bool
     */
    private $initialised = false;

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

    /**
     * @return Configuration
     */
    public static function getInstance(): Configuration
    {
        if (!(self::$instance instanceof Configuration)) {
            self::$instance = new Configuration();
        } elseif (self::$instance->initNeeded) {
            throw new ConfigurationInstantiationException('Configuration need initialisation after instantiation!');
        }

        if (!self::$instance->initNeeded && !self::$instance->initialised) {
            self::$instance->initNeeded = true;
        }

        return self::$instance;
    }

    /**
     * @return void
     */
    public static function destroyInstance(): void
    {
        self::$instance = null;
    }

    private function __construct()
    {
    }

    /**
     * @return void
     */
    public function init(AbstractConfigurationReader $configurationReader = null): void
    {
        $this->initNeeded = false;
        $this->initialised = true;

        if (!($configurationReader instanceof AbstractConfigurationReader)) {
            $configurationReader = new DefaultConfigurationReader();
        }

        $this->config = $configurationReader->getArrayCopy();
    }

    /**
     * @return LinterConfiguration
     */
    public function getLinterConfiguration(): LinterConfiguration
    {
        $configurationProcessor = new Processor();
        $this->linterConfiguration = new LinterConfiguration();
        $linterConfig  = $configurationProcessor->processConfiguration($this->linterConfiguration, [$this->config]);
        $this->linterConfiguration->setConfiguration($linterConfig);
        return $this->linterConfiguration;
    }


    /**
     * @return string
     */
    public function oneLevelIndentationString(): string
    {
        $this->getLinterConfiguration();
        $sniffs = $this->linterConfiguration->getSniffConfigurations();
        $parameters = [];

        foreach ($sniffs as $sniff) {
            if ($sniff['class'] === IndentationSniff::class) {
                $parameters = $sniff['parameters'];
                break;
            }
        }

        $indentationCharacter = ' ';
        $useSpaces = $parameters['useSpaces'] ?? false;
        if ($useSpaces === false) {
            $indentationCharacter = "\t";
        }
        $indentPerLevel = $parameters['indentPerLevel'] ?? 2;
        return str_repeat($indentationCharacter, $indentPerLevel);
    }
}
