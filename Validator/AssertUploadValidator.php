<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Validator;

use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertUploadValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AssertUpload) {
            throw new UnexpectedTypeException($constraint, AssertUpload::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if ($value instanceof AbstractUpload) {
            $value = $value->getFile()->getPathname();
        }

        $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, $constraint->constraints);
    }
}
