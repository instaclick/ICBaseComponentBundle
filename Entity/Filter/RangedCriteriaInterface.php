<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Entity\Filter;

/**
 * Ranged Criteria Interface.
 *
 * @author Danilo Cabello <daniloc@nationalfibre.net>
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
interface RangedCriteriaInterface
{
    /**
     * Retrieve the first result offset.
     *
     * @return integer
     */
    public function getFirstResult();

    /**
     * Define the first result offset.
     *
     * @param integer $firstResult
     */
    public function setFirstResult($firstResult);

    /**
     * Retrieve the maximum results per page.
     *
     * @return integer
     */
    public function getMaxResults();

    /**
     * Define the maximum results per page.
     *
     * @param integer $maxResults
     */
    public function setMaxResults($maxResults);
}
