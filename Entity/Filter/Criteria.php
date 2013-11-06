<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Entity\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Criteria.
 *
 * @author Anthon Pang <anthonp@nationalfibre.net>
 * @author Danilo Cabello <daniloc@nationalfibre.net>
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
class Criteria extends QueryBuilder implements CriteriaInterface, RangedCriteriaInterface
{
    /**
     * Constructor
     *
     * @param \Doctrine\ORM\EntityManager $em         Entity manager
     * @param string                      $entityName Entity name
     * @param string                      $alias      Alias
     */
    public function __construct(EntityManager $em, $entityName, $alias = 'e')
    {
        parent::__construct($em);

        $this->select($alias)
             ->from($entityName, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function add($dqlPartName, $dqlPart, $append = false)
    {
        // Prevent overriding already defined elements
        if (in_array($dqlPartName, array('select', 'from'))) {
            $append = true;
        }

        return parent::add($dqlPartName, $dqlPart, $append);
    }

    /**
     * {@inheritdoc}
     */
    public function resetDQLParts($parts = null)
    {
        // Do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function resetDQLPart($part)
    {
        // Do nothing
    }
}
