<?php

namespace UserApi\Validation;

use UserApi\Enum\ValidationEnum;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class SimpleObjectValidator
 * @package App\Validation
 */
class SimpleObjectValidator
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * SimpleObjectValidator constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $object
     *
     * @throws ValidatorException
     */
    public function validate(object $object) : void
    {
        $errors = $this->validator->validate($object);
        if ($errors->count() >= ValidationEnum::MIN_ERRORS_COUNT) {
            throw new BadRequestException($this->getErrorMessage($errors));
        }
    }

    /**
     * @param ConstraintViolationListInterface $constraintViolationList
     * @return string
     */
    private function getErrorMessage(ConstraintViolationListInterface $constraintViolationList): string
    {
        $errorMessages = [];
        foreach ($constraintViolationList as $constraintViolation) {
            $errorMessages[] = $constraintViolation->getMessage();
        }

        return implode(ValidationEnum::SEMICOLON_SEPARATOR, $errorMessages);
    }
}