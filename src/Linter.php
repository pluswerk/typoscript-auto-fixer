<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer;

use Exception;
use Helmich\TypoScriptLint\Linter\LinterConfiguration;
use Helmich\TypoScriptLint\Linter\Sniff\SniffLocator;
use Helmich\TypoScriptParser\Parser\Parser;
use Helmich\TypoScriptParser\Tokenizer\Tokenizer;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueFactory;
use Symfony\Component\Config\Definition\Processor;

final class Linter
{
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
        $config = [
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

        $configurationProcessor = new Processor();
        $configuration = new LinterConfiguration();
        $linterConfig = $configurationProcessor->processConfiguration($configuration, [$config]);
        $configuration->setConfiguration($linterConfig);
        $sniffLocator = new SniffLocator();

        $tokenizer = new Tokenizer();
        $parser = new Parser($tokenizer);

        $file = new \Helmich\TypoScriptLint\Linter\Report\File($filePath);

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
