<?php

namespace AppBundle\Error;

class ProductImportError
{
    /**
     * @var string
     */
    protected $productCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $productCode
     * @param string $message
     */
    public function __construct(string $productCode, string $message)
    {
        $this->productCode = $productCode;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getFullMessage()
    {
        return sprintf(
            'Product code: %s, message: %s',
            $this->getProductCode(),
            $this->getMessage()
        );
    }
}
