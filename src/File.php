<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer;

use Pluswerk\TypoScriptAutoFixer\Exception\FileNotWritableException;
use Pluswerk\TypoScriptAutoFixer\Exception\WriteFileFailedException;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;

final class File extends \SplFileInfo
{
    /**
     * @var IssueCollection
     */
    private $issues;

    public function __construct($file_name)
    {
        parent::__construct($file_name);
        $this->issues = new IssueCollection();
    }

    /**
     * @param int $line
     *
     * @return string
     */
    public function readLine(int $line): string
    {
        $fileObject = $this->openFile('r');
        $fileObject->seek($line - 1);
        return $fileObject->current();
    }

    /**
     * @param string $lineValue
     * @param int    $line
     *
     * @todo: Try to make this method a little bit nicer ;-).
     */
    public function replaceLine(string $lineValue, int $line): void
    {
        $fileObject = $this->openFile('r');
        $content = '';
        $fileObject->rewind();

        while (!$fileObject->eof()) {
            if ($fileObject->key() === ($line - 1)) {
                $content .= $lineValue;

                if (strpos($fileObject->current(), PHP_EOL) !== false) {
                    $content .= PHP_EOL;
                }
            } else {
                $content .= $fileObject->current();
            }

            $fileObject->next();
        }

        $this->overwriteFileContent($content);
    }

    /**
     * @param IssueCollection $issueCollection
     */
    public function updateIssueCollection(IssueCollection $issueCollection): void
    {
        $this->issues = $issueCollection;
    }

    /**
     * @param $content
     */
    private function overwriteFileContent($content): void
    {
        try {
            $writeFileObject = $this->openFile('w');
        } catch (\RuntimeException $e) {
            throw new FileNotWritableException($e->getMessage());
        }

        $writeResult = $writeFileObject->fwrite($content);

        if ($writeResult === false || $writeResult === null) {
            throw new WriteFileFailedException('write file ' . $this->getPathname() . ' failed!');
        }
    }

    /**
     * @return IssueCollection
     */
    public function issues(): IssueCollection
    {
        return $this->issues;
    }
}
