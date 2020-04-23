<?php

namespace Tests\AppBundle\Factory;

use AppBundle\Exception\FormatNotFoundException;
use AppBundle\Factory\ReaderFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Ddeboer\DataImport\Reader\CsvReader;

class ReaderFactoryTest extends TestCase
{
    /**
     * Testing get reader of the bad format.
     */
    public function testBadFormat()
    {
        $file = 'test.txt';

        $this->expectException(FormatNotFoundException::class);

        ReaderFactory::getReader('test', $file);
    }

    /**
     * Testing get reader of not exists file.
     */
    public function testNotExistsFile()
    {
        $file = 'test.csv';

        $this->expectException(FileNotFoundException::class);

        ReaderFactory::getReader('csv', $file);
    }

    /**
     * Testing get reader for valid data.
     */
    public function testExistsFile()
    {
        $file = $this->getValidFile();
        $reader = ReaderFactory::getReader('csv', $file->url());

        $this->assertInstanceOf(CsvReader::class, $reader);
    }

    /**
     * Simulate the file.
     *
     * @return vfsStreamFile
     */
    private function getValidFile()
    {
        $root = vfsStream::setup();

        return vfsStream::newFile('foo.csv', 0777)
            ->withContent(LargeFileContent::withKilobytes(100))
            ->at($root);
    }
}
