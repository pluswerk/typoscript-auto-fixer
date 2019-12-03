<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

final class Assignment
{
    /**
     * @var Identifier
     */
    private $identifier;

    /**
     * @var Operator
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * Assignment constructor.
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->operator = $this->detectOperator($string);
        [$identifierString, $this->value] = $this->splitByOperator($string);
        $this->identifier = new Identifier($identifierString);
    }

    /**
     * @return Identifier
     */
    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @return Operator
     */
    public function operator(): Operator
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param string $line
     *
     * @return Operator
     */
    private function detectOperator(string $line): Operator
    {
        if (strpos($line, '=<') !== false) {
            return Operator::createReference();
        }
        if (strpos($line, ':=') !== false) {
            return Operator::createModification();
        }
        if (strpos($line, '=') !== false) {
            return Operator::createEqual();
        }
        if (strpos($line, '<') !== false) {
            return Operator::createCopy();
        }
        if (strpos($line, '>') !== false) {
            return Operator::createDelete();
        }
        if (strpos($line, '(') !== false && strpos($line, ')') !== false) {
            return Operator::createMultiLine();
        }
        throw new \RuntimeException('Assignment needs an operator');
    }

    /**
     * @param string $string
     *
     * @return array
     */
    private function splitByOperator(string $string): array
    {
        if ($this->operator->isMultiLine()) {
            $array = explode('(', $string);
            $array[1] = trim(rtrim(trim($array[1]), ')'));
        } else {
            $array = array_filter(explode((string)$this->operator, $string));
        }

        if (!isset($array[1])) {
            $array[1] = '';
        }

        foreach ($array as $key => $item) {
            $array[$key] = trim($item);
        }

        return $array;
    }
}
