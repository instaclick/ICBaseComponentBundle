<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Service\Filter;

use Doctrine\ORM\EntityManager;

/**
 * Dependent Entity Form Filter Service
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class DependentEntityFormFilterService implements DependentEntityFormFilterServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCriteria(EntityManager $entityManager, $value, $optionList)
    {
        $repository = $entityManager->getRepository($optionList['class']);
        $criteria   = $repository->newCriteria('e');

        $criteria->andWhere('e.'. $optionList['dependentProperty'] .' = :dependentProperty');
        $criteria->setParameter(':dependentProperty', $value);

        return $criteria;
    }
}
