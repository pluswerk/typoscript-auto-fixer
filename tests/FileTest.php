<?php
declare(strict_types=1);

namespace Pluswerk\TypoScriptAutoFixer\Tests;

use PHPUnit\Framework\TestCase;
use Pluswerk\TypoScriptAutoFixer\Exception\FileNotWritableException;
use Pluswerk\TypoScriptAutoFixer\File;
use Pluswerk\TypoScriptAutoFixer\Issue\AbstractIssue;
use Pluswerk\TypoScriptAutoFixer\Issue\IssueCollection;

/**
 * Class FileTest
 * @package Pluswerk\TypoScriptAutoFixer\Tests
 * @covers Pluswerk\TypoScriptAutoFixer\File
 */
final class FileTest extends TestCase
{
    /**
     * @var File
     */
    private $file;

    protected function setUp(): void
    {
        $this->file = new File(__DIR__ . '/Fixtures/test.txt');
    }

    /**
     * @test
     * @dataProvider fileContentProvier
     */
    public function canFetchCertainLine($expected, $line): void
    {
        $this->assertSame($expected, $this->file->readLine($line));
    }

    public function fileContentProvier()
    {
        return [
            'line one' => [
                'expected' => 'line one value' . PHP_EOL,
                'line' => 1
            ],
            'line two' => [
                'expected' => 'line two value' . PHP_EOL,
                'line' => 2
            ],
            'line three' => [
                'expected' => 'line three value' . PHP_EOL,
                'line' => 3
            ],
            'line four' => [
                'expected' => 'line four value',
                'line' => 4
            ],
        ];
    }

    /**
     * @test
     * @dataProvider fileContentWriteProvider
     */
    public function canOverwriteLine($expected, $lineValue, $line, $filePathName)
    {
        if (is_file(__DIR__ . '/Fixtures/tmp_test.txt')) {
            unlink(__DIR__ . '/Fixtures/tmp_test.txt');
        }
        copy($filePathName, __DIR__ . '/Fixtures/tmp_test.txt');
        $file = new File(__DIR__ . '/Fixtures/tmp_test.txt');
        $file->replaceLine($lineValue, $line);
        $this->assertSame($expected, file_get_contents(__DIR__ . '/Fixtures/tmp_test.txt'));
        unlink(__DIR__ . '/Fixtures/tmp_test.txt');
    }

    public function fileContentWriteProvider(): array
    {
        $largeFile = file_get_contents(__DIR__ . '/Fixtures/expected_reslut.txt');
        return[
            'line with line ending' => [
                'expected' => 'line one value' . PHP_EOL
                              . 'line two value' . PHP_EOL
                              . 'substituted line' . PHP_EOL
                              . 'line four value',
                'lineValue' => 'substituted line',
                'line' => 3,
                'filePathName' => __DIR__ . '/Fixtures/test.txt'
            ],
            'line without line ending' => [
                'expected' => 'line one value' . PHP_EOL
                              . 'line two value' . PHP_EOL
                              . 'line three value' . PHP_EOL
                              . 'substituted line',
                'lineValue' => 'substituted line',
                'line' => 4,
                'filePathName' => __DIR__ . '/Fixtures/test.txt'
            ],
            'large file (>3000 lines)' => [
                'expected' => $largeFile,
                'lineValue' => 'this is a changed test file line',
                'line' => 2424,
                'filePathName' => __DIR__ . '/Fixtures/large_file_test.txt'
            ]
        ];
    }

    /**
     * @test
     */
    public function ifFileIsNotWritableAnExceptionIsThrown(): void
    {
        $file = new File(__DIR__ . '/Fixtures/not_writable_file.txt');
        $this->expectException(FileNotWritableException::class);
        $file->replaceLine('failing overwrite', 3);
    }

    /**
     * @test
     */
    public function aFileHoldsAnEmptyIssueCollectionByDefault(): void
    {
        $file = new File(__DIR__ . '/Fixtures/test.txt');
        $issueCollection = new IssueCollection();
        $this->assertEquals($issueCollection, $file->issues());
    }

    /**
     * @test
     */
    public function theIssueCollectionCanBeUpdated(): void
    {
        $file = new File(__DIR__ . '/Fixtures/test.txt');
        $issueCollection = new IssueCollection();
        $issue = $this->createMock(AbstractIssue::class);
        $issueCollection->add($issue);
        $file->updateIssueCollection($issueCollection);
        $this->assertEquals($issueCollection, $file->issues());
    }
}
