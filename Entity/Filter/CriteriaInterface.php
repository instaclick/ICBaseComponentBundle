<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Entity\Filter;

/**
 * Criteria Interface
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
interface CriteriaInterface
{
    /**
     * Retrieve a Query
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQuery();
}
