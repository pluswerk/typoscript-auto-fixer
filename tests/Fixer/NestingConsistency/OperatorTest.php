<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Operator;

/**
 * Class OperatorTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests\Fixer\NestedConsistency
 * @covers \Pluswerk\TypoScriptAutoFixer\Fixer\NestingConsistency\Operator
 */
final class OperatorTest extends TestCase
{
    /**
     * @test
     */
    public function equalsOperatorCanBeCreated(): void
    {
        $operator = Operator::createEqual();
        $this->assertEquals('=', (string)$operator);
    }

    /**
     * @test
     */
    public function referenceOperatorCanBeCreated(): void
    {
        $operator = Operator::createReference();
        $this->assertEquals('=<', (string)$operator);
    }

    /**
     * @test
     */
    public function copyOperatorCanBeCreated(): void
    {
        $operator = Operator::createCopy();
        $this->assertEquals('<', (string)$operator);
    }

    /**
     * @test
     */
    public function deleteOperatorCanBeCreated(): void
    {
        $operator = Operator::createDelete();
        $this->assertEquals('>', (string)$operator);
    }

    /**
     * @test
     */
    public function modificationsOperatorCanBeCreated(): void
    {
        $operator = Operator::createModification();
        $this->assertEquals(':=', (string)$operator);
    }

    /**
     * @test
     */
    public function multiLineOperatorCanBeCreated(): void
    {
        $operator = Operator::createMultiLine();
        $this->assertEquals('()', (string)$operator);
    }

    /**
     * @test
     */
    public function checkOperatorIsMultiLineOperator(): void
    {
        $operator = Operator::createMultiLine();
        $this->assertTrue($operator->isMultiLine());
    }

    /**
     * @test
     */
    public function checkOperatorIsCopyOperator(): void
    {
        $operator = Operator::createCopy();
        $this->assertTrue($operator->isCopy());
    }

    /**
     * @test
     */
    public function checkOperatorIsReferenceOperator(): void
    {
        $operator = Operator::createReference();
        $this->assertTrue($operator->isReference());
    }
}
