<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * A custom constraint for validation entities are not equal.
 *
 * @Annotation
 * @codeCoverageIgnore
 *
 * @author Oleksandr Kovalov <oleksandrk@nationalfibre.net>
 * @author Oleksii Strutsynskyi <oleksiis@nationalfibre.net>
 */
class EntityNotIdentical extends Constraint
{
    /**
     * Source entity to compare with
     *
     * @var string
     */
    public $sourceEntity;

    /**
     * Target entity to compare with
     *
     * @var string
     */
    public $targetEntity;

    /**
     * @var string
     */
    public $message = 'ic_base_component.validator.error_message.entity_not_identical';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ic_base_component.validator.entity_not_identical';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
