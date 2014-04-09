<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Service\Filter;

use Doctrine\ORM\EntityManager;

/**
 * Dependent Entity Form Filter Interface
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
interface DependentEntityFormFilterServiceInterface
{
    /**
     * Build the criteria with the dependency restriction.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository $entityManager
     * @param mixed                                                              $value
     * @param array                                                              $optionList
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function buildCriteria(EntityManager $entityManager, $value, $optionList);
}
