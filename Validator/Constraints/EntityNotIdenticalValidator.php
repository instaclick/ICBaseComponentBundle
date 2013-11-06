<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Validator to compare that entities are not identical
 *
 * @author Oleksandr Kovalov <oleksandrk@nationalfibre.net>
 * @author Oleksii Strutsynskyi <oleksiis@nationalfibre.net>
 */
class EntityNotIdenticalValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($model, Constraint $constraint)
    {
        $propertyAccessor = PropertyAccess::getPropertyAccessor();

        $sourceEntity = $propertyAccessor->getValue($model, $constraint->sourceEntity);
        $targetEntity = $propertyAccessor->getValue($model, $constraint->targetEntity);

        if ( ! $sourceEntity || ! $targetEntity) {
            return;
        }

        if ($sourceEntity->getId() === $targetEntity->getId()) {
            $this->context->addViolationAt($constraint->targetEntity, $constraint->message);
        }
    }
}
