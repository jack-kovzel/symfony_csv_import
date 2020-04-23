<?php

namespace AppBundle\Service;

use AppBundle\Error\ProductImportError;
use AppBundle\Helper\ProductHelper;
use AppBundle\Step\ValidatorStep;
use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Step\FilterStep;
use Ddeboer\DataImport\Step\MappingStep;
use Ddeboer\DataImport\Step\ValueConverterStep;
use Ddeboer\DataImport\Workflow\StepAggregator as Workflow;
use Ddeboer\DataImport\Writer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

class ProductImportService
{
    const PRODUCT_MAX_COST = 1000;

    const PRODUCT_CONDITION_COST = 5;
    const PRODUCT_CONDITION_STOCK = 10;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var ProductHelper
     */
    protected $helper;

    /**
     * @var int
     */
    protected $totalProcessedCount = 0;

    /**
     * @var int
     */
    protected $successCount = 0;

    /**
     * @var ProductImportError[]
     */
    protected $errors = [];

    /**
     * @param Validator $validator
     * @param ProductHelper $helper
     */
    public function __construct(Validator $validator, ProductHelper $helper)
    {
        $this->validator = $validator;
        $this->helper = $helper;
    }

    /**
     * @return int
     */
    public function getTotalProcessedCount()
    {
        return $this->totalProcessedCount;
    }

    /**
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    /**
     * @return int
     */
    public function getUniqueErrorsCount()
    {
        $uniqueErrors = [];

        foreach ($this->errors as $error) {
            $uniqueErrors[$error->getProductCode()] = $error;
        }

        return count($uniqueErrors);
    }

    /**
     * @return ProductImportError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Reader $reader
     * @param Writer $writer
     */
    public function doImport(Reader $reader, Writer $writer)
    {
        $reader->setHeaderRowNumber(0);

        $mappingStep = new MappingStep($this->helper->getMapping());

        $converterStep = new ValueConverterStep();
        $converterStep
            ->add('[dateDiscontinued]', function ($item) {
                return 'yes' == $item ? new \DateTime() : null;
            });

        $filter = new ValidatorStep($this->validator);
        $filter->throwExceptions();
        $filter->add('cost', new Assert\LessThan([
            'value' => self::PRODUCT_MAX_COST,
            'message' => 'Cost should be less than {{ compared_value }}',
        ]));

        foreach ($this->helper->getConstraints() as $attribute => $constraints) {
            foreach ($constraints as $constraint) {
                $filter->add($attribute, $constraint);
            }
        }

        $costAndStockFilter = new FilterStep();
        $costAndStockFilter->add(function ($item) {
            if ($item['cost'] < self::PRODUCT_CONDITION_COST && $item['stock'] < self::PRODUCT_CONDITION_STOCK) {
                $message = 'Cost < '.self::PRODUCT_CONDITION_COST.' and Stock < '.self::PRODUCT_CONDITION_STOCK.' ';
                $error = new ProductImportError($item['productCode'], $message);
                $this->storeError($error);

                return false;
            }

            return true;
        });

        $workflow = new Workflow($reader);
        $workflow->setSkipItemOnFailure(true);

        $result = $workflow
            ->addStep($mappingStep, 4)
            ->addStep($converterStep, 3)
            ->addStep($filter, 2)
            ->addStep($costAndStockFilter, 1)
            ->addWriter($writer)
            ->process();

        $this->totalProcessedCount = $result->getTotalProcessedCount() + $this->getUniqueErrorsCount();
        $this->successCount = $result->getSuccessCount();

        if ($result->hasErrors()) {
            foreach ($result->getExceptions() as $exception) {
                /* @var $violation ConstraintViolation */
                foreach ($exception->getViolations() as $violation) {
                    $error = new ProductImportError($violation->getRoot()['productCode'], $violation->getMessage());
                    $this->storeError($error);
                }
            }
        }
    }

    /**
     * @param ProductImportError $error
     *
     * @return $this
     */
    protected function storeError(ProductImportError $error)
    {
        $this->errors[] = $error;

        return $this;
    }
}
