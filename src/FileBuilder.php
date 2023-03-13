<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer;

use Exception;
use Pluswerk\TypoScriptAutoFixer\Adapter\Linter;

class FileBuilder
{
    /**
     * @var Linter
     */
    private Linter $linter;

    /**
     * FileBuilder constructor.
     *
     * @param Linter|null $linter
     */
    public function __construct(Linter $linter = null)
    {
        $this->linter = $linter ?? new Linter();
    }

    /**
     * @param string $filePath
     *
     * @return File
     * @throws Exception
     */
    public function buildFile(string $filePath): File
    {
        $file = new File($filePath);

        $issueCollection = $this->linter->lint($filePath);

        $file->updateIssueCollection($issueCollection);

        return $file;
    }
}
