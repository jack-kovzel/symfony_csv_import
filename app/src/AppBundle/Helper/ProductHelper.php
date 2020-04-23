<?php

namespace AppBundle\Helper;

use AppBundle\Entity\Product;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

class ProductHelper
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    public function getMapping(): array
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

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        $constraints = [];

        /* @var $metadata ClassMetadata */
        $metadata = $this->validator->getMetadataFor(new Product());
        foreach ($metadata->properties as $attribute => $propertyMetadata) {
            foreach ($propertyMetadata->getConstraints() as $constraint) {
                $constraints[$attribute][] = $constraint;
            }
        }

        return $constraints;
    }
}
