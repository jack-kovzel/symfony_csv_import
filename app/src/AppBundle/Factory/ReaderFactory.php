<?php

namespace AppBundle\Factory;

use AppBundle\Exception\FormatNotFoundException;
use Ddeboer\DataImport\Reader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ReaderFactory
{
    /**
     * @param string $format
     * @param string $target
     *
     * @return Reader\CsvReader|null
     *
     * @throws FormatNotFoundException
     */
    public static function getReader($format, $target)
    {
        $instance = null;

        switch ($format) {
            case 'csv':
                $instance = self::getCsvReader($target);
                break;
            default:
                throw new FormatNotFoundException('Format not found');
        }

        return $instance;
    }

    /**
     * @param string $filePath
     *
     * @return Reader\CsvReader
     */
    protected static function getCsvReader(string $filePath)
    {
        try {
            $filePath = new \SplFileObject($filePath);
            $instance = new Reader\CsvReader($filePath);
            $instance->setHeaderRowNumber(0); //set headers
            return $instance;
        } catch (\Exception $ex) {
            throw new FileNotFoundException();
        }
    }
}
