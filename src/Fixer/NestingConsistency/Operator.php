<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency;

final class Operator
{
    private const EQUAL = '=';
    private const REFERENCE =  '=<';
    private const COPY = '<';
    private const DELETE = '>';
    private const MODIFICATION = ':=';
    public const MULTI_LINE = '()';

    /**
     * @var string
     */
    private $operator;

    /**
     * @return Operator
     */
    public static function createEqual(): Operator
    {
        return new self(self::EQUAL);
    }

    /**
     * @return Operator
     */
    public static function createReference(): Operator
    {
        return new self(self::REFERENCE);
    }

    /**
     * @return Operator
     */
    public static function createCopy(): Operator
    {
        return new self(self::COPY);
    }

    /**
     * @return Operator
     */
    public static function createDelete(): Operator
    {
        return new self(self::DELETE);
    }

    /**
     * @return Operator
     */
    public static function createModification(): Operator
    {
        return new self(self::MODIFICATION);
    }

    /**
     * @return Operator
     */
    public static function createMultiLine(): Operator
    {
        return new self(self::MULTI_LINE);
    }

    /**
     * Operator constructor.
     *
     * @param string $operatorString
     */
    private function __construct(string $operatorString)
    {
        $this->operator = $operatorString;
    }

    /**
     * @return bool
     */
    public function isMultiLine(): bool
    {
        return $this->operator === self::MULTI_LINE;
    }

    /**
     * @return bool
     */
    public function isCopy(): bool
    {
        return $this->operator === self::COPY;
    }

    /**
     * @return bool
     */
    public function isReference(): bool
    {
        return $this->operator === self::REFERENCE;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->operator;
    }
}
