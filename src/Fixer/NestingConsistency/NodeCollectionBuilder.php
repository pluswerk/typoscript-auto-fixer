<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

class NodeCollectionBuilder
{
    /**
     * @param NodeCollection $nodesA
     * @param NodeCollection $nodesB
     *
     * @return NodeCollection
     */
    public function mergeNodeCollections(NodeCollection $nodesA, NodeCollection $nodesB): NodeCollection
    {
        foreach ($nodesB as $node) {
            if (!$nodesA->hasNode($node->identifier())) {
                $nodesA->add($node);
                continue;
            }

            if ($nodesA->getNode($node->identifier())->hasValue() && $nodesB->getNode($node->identifier())->hasValue()) {
                throw new \RuntimeException('Overwrite assignment!');
            }

            if ($nodesA->getNode($node->identifier())->hasChildren() && $nodesB->getNode($node->identifier())->hasChildren()) {
                $col = $this->mergeNodeCollections($nodesA->getNode($node->identifier())->children(), $nodesB->getNode($node->identifier())->children());

                $value = '';
                $operator = null;

                if ($nodesA->getNode($node->identifier())->value() !== null) {
                    $value = $nodesA->getNode($node->identifier())->value();
                } elseif ($nodesB->getNode($node->identifier())->value() !== null) {
                    $value = $nodesB->getNode($node->identifier())->value();
                }

                if ($nodesA->getNode($node->identifier())->operator() !== null) {
                    $operator = $nodesA->getNode($node->identifier())->operator();
                } elseif ($nodesB->getNode($node->identifier())->value() !== null) {
                    $operator = $nodesB->getNode($node->identifier())->operator();
                }

                $subNode = new Node($node->identifier(), $value, $operator);
                $subNode->updateCollection($col);
                $nodesA->add($subNode);
            } elseif ($nodesA->getNode($node->identifier())->hasValue() && $nodesB->getNode($node->identifier())->hasChildren()) {
                $nodesA->getNode($node->identifier())->updateCollection($nodesB->getNode($node->identifier())->children());
            } elseif ($nodesA->getNode($node->identifier())->hasChildren() && $nodesB->getNode($node->identifier())->hasValue()) {
                $newNode = new Node($node->identifier(), $nodesB->getNode($node->identifier())->value(), $nodesB->getNode($node->identifier())->operator());
                $newNode->updateCollection($nodesA->getNode($node->identifier())->children());
                $nodesA->add($newNode);
            }
        }

        // Update node levels for whole tree
        foreach ($nodesA as $node) {
            $node->updateLevel(0);
        }

        return $nodesA;
    }

    /**
     * @param string $string
     *
     * @return NodeCollection
     */
    public function buildNodeCollectionFromSingleAssignment(string $string): NodeCollection
    {
        $assignment = new Assignment($string);
        return $this->buildNodeCollectionFromAssignment($assignment);
    }

    /**
     * @param array $lines
     *
     * @return NodeCollection
     * @todo Refactor this method somehow... it's still ugly
     */
    public function buildNodeCollectionFromMultiLine(array $lines): NodeCollection
    {
        $nodeCollection = new NodeCollection();

        $assignments = [];
        $prefixes = [];
        $level = 0;
        $inMultiLineValue = false;
        $multiLineValue = '';
        $leftValue = '';
        foreach ($lines as $line) {
            if (!$inMultiLineValue) {
                $line = trim($line);
            }
            if ($inMultiLineValue) {
                if (substr(trim($line), -1, 1) === ')') {
                    $lineString = (empty($prefixes))
                        ? $leftValue . '(' . PHP_EOL . $multiLineValue . ')'
                        : implode('.', $prefixes) . '.' . $leftValue . '(' . PHP_EOL . $multiLineValue . ')';
                    $assignments[] = new Assignment($lineString);
                    $multiLineValue = '';
                    $leftValue = '';
                    $inMultiLineValue = false;
                } else {
                    $multiLineValue .= $line;
                }
            } elseif (substr(trim($line), -1, 1) === '{') {
                $prefixes[$level] = trim(rtrim($line, '{'));
                $level++;
            } elseif (substr(trim($line), -1, 1) === '}') {
                $level--;
                unset($prefixes[$level]);
            } elseif (substr(trim($line), -1, 1) === '(') {
                $leftValue = trim(rtrim(trim($line), '('));
                $inMultiLineValue = true;
            } elseif (trim($line) !== '') {
                $lineString = (empty($prefixes))
                    ? trim(rtrim($line, '{'))
                    : implode('.', $prefixes) . '.' . trim(rtrim($line, '{'));
                $assignments[] = new Assignment($lineString);
            }
        }

        foreach ($assignments as $assignment) {
            $nodeCollection = $this->mergeNodeCollections($nodeCollection, $this->buildNodeCollectionFromAssignment($assignment));
        }

        return $nodeCollection;
    }

    /**
     * @param \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Assignment $assignment
     *
     * @return NodeCollection
     */
    private function buildNodeCollectionFromAssignment(Assignment $assignment): NodeCollection
    {
        $node = null;
        $childNode = null;
        $nodeCollection = new NodeCollection();

        foreach ($assignment->identifier()->reverseItems() as $item) {
            if ($node === null) {
                $tmpNode = new Node($item, $assignment->value(), $assignment->operator());
                $node = $tmpNode;
            } else {
                $tmpNode = new Node($item);
                $tmpNode->addChildNode($node);
                $node = $tmpNode;
            }
        }

        $nodeCollection->add($node);
        return $nodeCollection;
    }
}
