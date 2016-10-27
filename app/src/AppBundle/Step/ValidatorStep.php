<?php

namespace AppBundle\Step;

use Ddeboer\DataImport\Exception\ValidationException;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorStep extends \Ddeboer\DataImport\Step\ValidatorStep
{
    /**
     * @var array
     */
    protected $constraints = [];

    /**
     * @var array
     */
    protected $violations = [];

    /**
     * @var bool
     */
    protected $throwExceptions = false;

    /**
     * @var int
     */
    protected $line = 1;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected $allowExtraFields = false;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        parent::__construct($validator);
    }

    /**
     * @param bool $allowExtraFields
     */
    public function setAllowExtraFields($allowExtraFields)
    {
        $this->allowExtraFields = (bool) $allowExtraFields;
    }

    public function throwExceptions($flag = true)
    {
        $this->throwExceptions = $flag;
    }

    /**
     * @param string     $field
     * @param Constraint $constraint
     *
     * @return $this
     */
    public function add($field, Constraint $constraint)
    {
        if (!isset($this->constraints[$field])) {
            $this->constraints[$field] = [];
        }

        $this->constraints[$field][] = $constraint;

        return $this;
    }

    /**
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    public function process(&$item)
    {
        $constraints = new Constraints\Collection($this->constraints);
        $constraints->allowExtraFields = true;

        $list = $this->validator->validate($item, $constraints);

        if (count($list) > 0) {
            $this->violations[$this->line] = $list;

            if ($this->throwExceptions) {
                throw new ValidationException($list, $this->line);
            }
        }

        ++$this->line;

        return 0 === count($list);
    }
}
