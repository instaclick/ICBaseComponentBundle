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
     * Whether to use second level cache, if available.
     *
     * @var boolean
     */
    protected $cacheable = false;

    /**
     * Second level cache region name.
     *
     * @var string|null
     */
    protected $cacheRegion;

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
     * Enable/disable second level query (result) caching for this query.
     *
     * @param boolean $cacheable
     *
     * @return \Doctrine\ORM\AbstractQuery This query instance.
     */
    public function setCacheable($cacheable)
    {
        $this->cacheable = (boolean) $cacheable;

        return $this;
    }

    /**
     * @param string $cacheRegion
     *
     * @return \Doctrine\ORM\AbstractQuery This query instance.
     */
    public function setCacheRegion($cacheRegion)
    {
        $this->cacheRegion = (string) $cacheRegion;

        return $this;
    }

    /**
    * Obtain the name of the second level query cache region in which query results will be stored
    *
    * @return The cache region name; NULL indicates the default region.
    */
    public function getCacheRegion()
    {
        return $this->cacheRegion;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return parent::getQuery()
            ->setCacheable($this->cacheable)
            ->setCacheRegion($this->cacheRegion);
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
