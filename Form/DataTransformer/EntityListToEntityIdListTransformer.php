<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\DataTransformer;

use IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use Doctrine\DBAL\Connection;

/**
 * The general-purpose DataTransformer that interfaces the data list between an entity and an id
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class EntityListToEntityIdListTransformer implements DataTransformerInterface
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository
     */
    private $entityRepository;

    /**
     * @var string
     */
    private $namedCriteria;

    /**
     * Constructor
     *
     * @param EntityRepository $entityRepository Entity repository
     * @param string           $namedCriteria    Named criteria
     */
    public function __construct(EntityRepository $entityRepository, $namedCriteria = null)
    {
        $this->entityRepository = $entityRepository;
        $this->namedCriteria    = $namedCriteria;
    }

    /**
     * Transforms an entity list to a list of entity id.
     *
     * @param array $entityList
     *
     * @return mixed
     */
    public function transform($entityList)
    {
        $entityIdList = array();

        if (null === $entityList || ! count($entityList)) {
            return $entityIdList;
        }

        foreach ($entityList as $entity) {
            $entityIdList[] = $entity !== null ? $entity->getId() : null;
        }

        return $entityIdList;
    }

    /**
     * Transforms an integer id to an entity.
     *
     * @param array $entityIdList
     *
     * @return array
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException If a matching entity is not found.
     */
    public function reverseTransform($entityIdList)
    {
        if ( ! count($entityIdList)) {
            return array();
        }

        $entityList = $this->fetchEntityList($entityIdList);

        $entityListResult = array_fill_keys($entityIdList, null);

        foreach ($entityList as $entity) {
            $entityListResult[$entity->getId()] = $entity;
        }

        $emptyEntityId = array_search(null, $entityListResult);

        if ($emptyEntityId !== false) {
            throw new TransformationFailedException(
                'An ' . $this->entityRepository->getEntityName() . 'entity with id "' . $emptyEntityId . '" does not exist.',
                500
            );
        }

        return $entityListResult;
    }

    /**
     * Build filter criteria for reverse transformation
     *
     * @param array $entityIdList
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria
     */
    private function fetchEntityList($entityIdList)
    {
        $criteria = $this->getCriteria();

        $criteria->setParameter('entityList', $entityIdList, Connection::PARAM_INT_ARRAY);

        return $this->entityRepository->filter($criteria);
    }

    /**
     * Get the named query, if it was defined, or the default criteria
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria
     */
    private function getCriteria()
    {
        if ($this->namedCriteria) {
            return $this->entityRepository->newNamedCriteria($this->namedCriteria);
        }

        return $this->entityRepository->newCriteria('e')->where('e IN (:entityList)');
    }
}
