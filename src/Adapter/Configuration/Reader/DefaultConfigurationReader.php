<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Reader;

final class DefaultConfigurationReader extends AbstractConfigurationReader
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
     * DefaultConfigurationReader constructor.
     */
    public function __construct()
    {
        parent::__construct($this->config);
    }
}
