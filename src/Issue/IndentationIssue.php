<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Issue;

use Pluswerk\TypoScriptAutoFixer\Exception\InvalidIndentationCharacterException;

final class IndentationIssue extends AbstractIssue
{
    private const ALLOWED_INDENTATION_CHARS = [' ', "\t"];

    /**
     * @var int
     */
    private $amountOfIndentChars;

    /**
     * @var string
     */
    private $indentationCharacter;

    /**
     * IndentationIssue constructor.
     *
     * @param int    $line
     * @param int    $amountOfIndentChars
     * @param string $indentationCharacter
     */
    public function __construct(int $line, int $amountOfIndentChars, string $indentationCharacter = ' ')
    {
        parent::__construct($line);

        $this->amountOfIndentChars = $amountOfIndentChars;

        if (!in_array($indentationCharacter, self::ALLOWED_INDENTATION_CHARS)) {
            throw new InvalidIndentationCharacterException('Given indentation character ' . $indentationCharacter . ' is invalid!');
        }

        $this->indentationCharacter = $indentationCharacter;
    }

    /**
     * @return int
     */
    public function amountOfIndentChars(): int
    {
        return $this->amountOfIndentChars;
    }

    /**
     * @return string
     */
    public function indentationCharacter(): string
    {
        return $this->indentationCharacter;
    }
}
