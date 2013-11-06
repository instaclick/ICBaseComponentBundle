<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\DataTransformer;

use IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * The general-purpose DataTransformer that interfaces the data back and forth in between
 * 1) Base Entity entity
 * 2) Base Entity entity identifier
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 * @author John Cartwright <johnc@nationalfibre.net>
 * @author Oleksandr Kovalov <oleksandrk@nationalfibre.net>
 *
 */
class EntityToEntityIdTransformer implements DataTransformerInterface
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository
     */
    protected $entityRepository;

    /**
     * @var mixed
     */
    protected $defaultId;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * Constructor
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository $entityRepository Entity repository
     * @param string                                                             $property         Property to use against query lookup
     * @param string                                                             $defaultId        Default ID
     */
    public function __construct(EntityRepository $entityRepository, $property, $defaultId)
    {
        $this->entityRepository = $entityRepository;
        $this->defaultId        = $defaultId;
        $this->property         = $property;
        $this->propertyAccessor = PropertyAccess::getPropertyAccessor();
    }

    /**
     * Transforms an entity to an integer id.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Entity $entity
     *
     * @return integer
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return $this->defaultId;
        }

        $propertyValue = $this->propertyAccessor->getValue($entity, $this->property);

        return $propertyValue;
    }

    /**
     * Transforms an identifier to an entity.
     *
     * @param integer $identifier
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Entity
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException If a matching entity is not found.
     */
    public function reverseTransform($identifier)
    {
        if ( ! $identifier) {
            return null;
        }

        $criteria = $this->createCriteria($identifier);
        $entity   = $this->entityRepository->filter($criteria);

        if ( ! $entity) {
            throw new TransformationFailedException(
                'An ' . $this->entityRepository->getEntityName() . 'entity with id "' . $identifier . '" does not exist.',
                500
            );
        }

        return $entity;
    }

    /**
     * Create the criteria for the identifier lookup
     *
     * @param string $identifier
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria
     */
    private function createCriteria($identifier)
    {
        $criteria = $this->entityRepository->newCriteria('e');

        $criteria->andWhere(sprintf('e.%s = :identifier', $this->property));
        $criteria->setParameter(':identifier', $identifier);
        $criteria->setMaxResults(1);

        return $criteria;
    }
}
