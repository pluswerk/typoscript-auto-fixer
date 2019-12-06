<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader;

use Pluswerk\TypoScriptAutoFixer\Exception\FailedReadConfigurationException;
use Symfony\Component\Yaml\Yaml;

final class YamlConfigurationReader extends AbstractConfigurationReader
{
    public function __construct(string $filePath = '')
    {
        if (!file_exists($filePath)) {
            $filePath = getcwd() . '/typoscript-lint.yml';
            if (!file_exists($filePath)) {
                throw new FailedReadConfigurationException($filePath . ' does not exist!');
            }
        }

        parent::__construct($this->readConfiguration($filePath));
    }

    /**
     * @param string $filePath
     *
     * @return array
     */
    private function readConfiguration(string $filePath): array
    {
        $yamlString = Yaml::parseFile($filePath);
        if (is_array($yamlString)) {
            return $yamlString;
        }
        return [];
    }
}
