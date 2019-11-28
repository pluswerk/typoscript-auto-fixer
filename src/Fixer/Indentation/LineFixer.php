<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\Indentation;

final class LineFixer
{
    /**
     * @param $line
     * @param $amountOfIndentChars
     * @param $character
     *
     * @return string
     */
    public function fixIndentation($line, $amountOfIndentChars, $character): string
    {
        $indent = str_repeat($character, $amountOfIndentChars);
        $line = ltrim($line);
        return $indent . $line;
    }
}
