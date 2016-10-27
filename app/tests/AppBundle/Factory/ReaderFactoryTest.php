<?php

namespace Tests\AppBundle\Factory;

use AppBundle\Exception\FormatNotFoundException;
use AppBundle\Factory\ReaderFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\content\LargeFileContent;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Ddeboer\DataImport\Reader\CsvReader;

/**
 * Class ReaderFactoryTest.
 */
class ReaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Testing get reader of the bad format.
     */
    public function testBadFormat()
    {
        $file = 'test.txt';
        $format = 'test';
        try {
            $reader = ReaderFactory::getReader($format, $file);
            $this->fail('Must throw FormatNotFoundException');
        } catch (FormatNotFoundException $ex) {
        }
    }
    /**
     * Testing get reader of not exists file.
     *
     * @throws FormatNotFoundException
     */
    public function testNotExistsFile()
    {
        $file = 'test.csv';
        $format = 'csv';
        try {
            $reader = ReaderFactory::getReader($format, $file);
            $this->fail('Must throw FileNotFoundException');
        } catch (FileNotFoundException $ex) {
        }
    }
    /**
     * Testing get reader for valid data.
     *
     * @throws FormatNotFoundException
     */
    public function testExistsFile()
    {
        $file = $this->getValidFile();
        $format = 'csv';
        $reader = ReaderFactory::getReader($format, $file->url());
        $this->assertInstanceOf(CsvReader::class, $reader);
    }
    /**
     * Simulate the file.
     *
     * @return $this
     */
    private function getValidFile()
    {
        $root = vfsStream::setup();

        return vfsStream::newFile('foo.csv', 0777)->withContent(LargeFileContent::withKilobytes(100))->at($root);
    }
}
