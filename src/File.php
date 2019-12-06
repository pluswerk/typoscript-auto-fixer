<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer;

use Pluswerk\TypoScriptAutoFixer\Exception\FileNotWritableException;
use Pluswerk\TypoScriptAutoFixer\Exception\WriteFileFailedException;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;

class File extends \SplFileInfo
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

    public function removeLine(int $line): void
    {
        $fileObject = $this->openFile('r');
        $content = '';
        $fileObject->rewind();

        while (!$fileObject->eof()) {
            if ($fileObject->key() === ($line - 1)) {
                // do not assign! It is the content of the line to remove.
                $fileObject->current();
            } else {
                $content .= $fileObject->current();
            }

            $fileObject->next();
        }

        $this->overwriteFileContent($content);
    }

    /**
     * @param int[] $lines
     */
    public function removeLines(array $lines): void
    {
        $fileObject = $this->openFile('r');
        $content = '';
        $fileObject->rewind();

        while (!$fileObject->eof()) {
            if (in_array($fileObject->key() + 1, $lines, true)) {
                // do not assign! It is the content of the line to remove.
                $fileObject->current();
            } else {
                $content .= $fileObject->current();
            }

            $fileObject->next();
        }

        $this->overwriteFileContent($content);
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
                $currentLine = $fileObject->current();
                if (strpos($lineValue, PHP_EOL) === false && strpos($currentLine, PHP_EOL) !== false) {
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
     * @param int    $line
     * @param string $string
     */
    public function insertStringToFile(int $line, string $string): void
    {
        $fileObject = $this->openFile('r');
        $content = '';
        $fileObject->rewind();

        while (!$fileObject->eof()) {
            if ($fileObject->key() === ($line - 1)) {
                $content .= $string;
                $content .= $fileObject->current();
            } else {
                $content .= $fileObject->current();
            }

            $fileObject->next();
        }

        $this->overwriteFileContent($content);
    }

    public function removeNeedlessEmptyLines(): void
    {
        $fileObject = $this->openFile('r');
        $content = '';
        $fileObject->rewind();

        $lastLineWasEmpty = false;

        $firstLine = $fileObject->current();
        if ($firstLine === PHP_EOL || $firstLine === '') {
            $fileObject->next();
        }

        while (!$fileObject->eof()) {
            $current = $fileObject->current();
            if (trim($current) === '') {
                $lastLineWasEmpty = true;
                $fileObject->next();
                continue;
            }
            if ($lastLineWasEmpty) {
                $lastLineWasEmpty = false;
                $content .= PHP_EOL;
            }
            $content .= $current;
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
