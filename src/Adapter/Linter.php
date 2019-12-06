<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Adapter;

use Exception;
use Helmich\TypoScriptLint\Linter\Report\File;
use Helmich\TypoScriptLint\Linter\Sniff\SniffLocator;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Pluswerk\TypoScriptAutoFixer\Adapter\Configuration\Configuration;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;

final class Linter
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param string $filePath
     *
     * @return IssueCollection
     * @throws Exception
     *
     * @todo REFACTOR - just copy paste ;)
     */
    public function lint(string $filePath): IssueCollection
    {
        $configuration = Configuration::getInstance()->getLinterConfiguration();

        $sniffLocator = new SniffLocator();

        $tokenizer = new Tokenizer();
        $parser = new Parser($tokenizer);

        $file = new File($filePath);

        $tokens     = $tokenizer->tokenizeStream($filePath);
        $statements = $parser->parseTokens($tokens);



        $sniffs = $sniffLocator->getTokenStreamSniffs($configuration);

        foreach ($sniffs as $sniff) {
            $sniffReport = $file->cloneEmpty();

            $sniff->sniff($tokens, $sniffReport, $configuration);

            $file = $file->merge($sniffReport);
        }



        $sniffs = $sniffLocator->getSyntaxTreeSniffs($configuration);

        foreach ($sniffs as $sniff) {
            $sniffReport = $file->cloneEmpty();

            $sniff->sniff($statements, $sniffReport, $configuration);

            $file = $file->merge($sniffReport);
        }

        $issueCollection =  new IssueCollection();
        $issueFactory = new IssueFactory();

        foreach ($file->getIssues() as $issue) {
            $newIssue = $issueFactory->getIssue($issue, $tokens);
            if ($newIssue instanceof AbstractIssue) {
                $issueCollection->add($newIssue);
            }
        }

        return $issueCollection;
    }
}
