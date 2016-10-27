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
     * @param $file
     *
     * @return Reader\CsvReader
     */
    protected static function getCsvReader($file)
    {
        try {
            $file = new \SplFileObject($file);
            $instance = new Reader\CsvReader($file);
            $instance->setHeaderRowNumber(0); //set headers
            return $instance;
        } catch (\Exception $ex) {
            throw new FileNotFoundException();
        }
    }
}
