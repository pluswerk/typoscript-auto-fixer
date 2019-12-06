<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Fixer\OperatorWhitespace;

class LineFixer
{
    /**
     * @param string $line
     *
     * @return string
     */
    public function fixOperatorWhitespace(string $line): string
    {
        $operator = $this->detectOperator($line);
        if ($operator === null) {
            return $line;
        }
        $parts = explode($operator, $line);
        $parts[0] = rtrim($parts[0]) . ' ';
        $parts[1] = ' ' . ltrim($parts[1]);
        return rtrim(implode($operator, $parts));
    }

    /**
     * @param string $line
     *
     * @return string|null
     */
    private function detectOperator(string $line): ?string
    {
        if (strpos($line, '=<') !== false) {
            return '=<';
        }
        if (strpos($line, ':=') !== false) {
            return ':=';
        }
        if (strpos($line, '=') !== false) {
            return '=';
        }
        if (strpos($line, '<') !== false) {
            return '<';
        }
        if (strpos($line, '>') !== false) {
            return '>';
        }
        return null;
    }
}
