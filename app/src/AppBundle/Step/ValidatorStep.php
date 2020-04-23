<?php

namespace AppBundle\Step;

use Ddeboer\DataImport\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Ddeboer\DataImport\Step\ValidatorStep as DdeboerValidatorStep;

class ValidatorStep extends DdeboerValidatorStep
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Constraint[]
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
     * @var bool
     */
    protected $allowExtraFields = false;

    /**
     * @param ValidatorInterface $validator
     */
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

    /**
     * @param bool $flag
     */
    public function throwExceptions($flag = true)
    {
        $this->throwExceptions = $flag;
    }

    /**
     * @param string $field
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

    /**
     * @param $item
     *
     * @return bool
     *
     * @throws ValidationException
     */
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
