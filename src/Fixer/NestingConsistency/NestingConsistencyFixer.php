<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\FileBuilder;
use Pluswerk\TypoScriptAutoFixer\Fixer\AbstractFixer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\NestingConsistencyIssue;

final class NestingConsistencyFixer extends AbstractFixer
{
    /**
     * @var NodeCollectionBuilder
     */
    private $nodeCollectionBuilder;

    public function __construct(FileBuilder $fileBuilder = null, NodeCollectionBuilder $nodeCollectionBuilder = null)
    {
        parent::__construct($fileBuilder);
        $this->nodeCollectionBuilder = $nodeCollectionBuilder ?? new NodeCollectionBuilder();
    }

    /**
     * @param File          $file
     * @param AbstractIssue|NestingConsistencyIssue $issue
     *
     * @return File
     */
    public function fixIssue(File $file, AbstractIssue $issue): File
    {
        if ($issue->line() < $issue->secondLine()) {
            $insertLine = $issue->secondLine();
            $lineNumbersA = range($issue->line(), $issue->firstEndLine());
            $lineNumbersB = range($issue->secondLine(), $issue->secondEndLine());
        } else {
            $insertLine = $issue->line();
            $lineNumbersA = range($issue->secondLine(), $issue->secondEndLine());
            $lineNumbersB = range($issue->line(), $issue->firstEndLine());
        }

        $linesA = [];
        $linesB = [];

        foreach ($lineNumbersA as $lineNumber) {
            $linesA[] = $file->readLine($lineNumber);
        }

        foreach ($lineNumbersB as $lineNumber) {
            $linesB[] = $file->readLine($lineNumber);
        }

        $collectionA = $this->nodeCollectionBuilder->buildNodeCollectionFromMultiLine($linesA);
        $collectionB = $this->nodeCollectionBuilder->buildNodeCollectionFromMultiLine($linesB);

        $resultCollection = $this->nodeCollectionBuilder->mergeNodeCollections($collectionA, $collectionB);

        $file->removeLines($lineNumbersB);

        $file->insertStringToFile($insertLine, (string) $resultCollection);

        $file->removeLines($lineNumbersA);

        $file = $this->fileBuilder->buildFile($file->getPathname());

        return $file;
    }
}
