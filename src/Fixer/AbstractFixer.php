<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer;

use Pluswerk\TypoScriptAutoFixer\FileBuilder;

abstract class AbstractFixer implements FixerInterface
{
    /**
     * @var FileBuilder
     */
    protected $fileBuilder;

    /**
     * AbstractFixer constructor.
     *
     * @param FileBuilder $fileBuilder
     */
    public function __construct(FileBuilder $fileBuilder = null)
    {
        $this->fileBuilder = $fileBuilder ?? new FileBuilder();
    }
}
