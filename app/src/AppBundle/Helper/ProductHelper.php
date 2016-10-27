<?php

namespace AppBundle\Helper;

use AppBundle\Entity\Product;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

class ProductHelper
{
    protected $validator;

    /**
     * ProductHelper constructor.
     *
     * @param $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function getMapping()
    {
        return [
            '[Product Code]' => '[productCode]',
            '[Product Name]' => '[productName]',
            '[Product Description]' => '[productDesc]',
            '[Stock]' => '[stock]',
            '[Cost in GBP]' => '[cost]',
            '[Discontinued]' => '[dateDiscontinued]',
        ];
    }

    public function getConstraints()
    {
        $constraints = [];

        /* @var $metadata \Symfony\Component\Validator\Mapping\ClassMetadata */
        $metadata = $this->validator->getMetadataFor(new Product());
        foreach ($metadata->properties as $attribute => $propertyMetadata) {
            foreach ($propertyMetadata->getConstraints() as $constraint) {
                $constraints[$attribute][] = $constraint;
            }
        }

        return $constraints;
    }
}
