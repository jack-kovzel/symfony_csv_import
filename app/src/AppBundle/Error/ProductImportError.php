<?php

namespace AppBundle\Error;

class ProductImportError
{
    /**
     * @var string
     */
    protected $pCode;
    /**
     * @var string
     */
    protected $message;

    /**
     * @return string
     */
    public function getPCode()
    {
        return $this->pCode;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $pCode
     * @param $message
     */
    public function __construct($pCode, $message)
    {
        $this->pCode = $pCode;
        $this->message = $message;
    }
}
