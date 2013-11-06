<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Entity\Filter;

use Doctrine\ORM\EntityManager;

/**
 * Named Filter criteria
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
class NamedCriteria implements CriteriaInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\ORM\Query
     */
    private $query;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\EntityManager $em         Entity manager
     * @param string                      $entityName Entity name
     * @param string                      $name       Name for the query
     */
    public function __construct(EntityManager $em, $entityName, $name)
    {
        $classMetadata = $em->getClassMetadata($entityName);
        $namedQueryDql = $classMetadata->getNamedQuery($name);

        $this->name  = $name;
        $this->query = $em->createQuery($namedQueryDql);
    }

    /**
     * Retrieve the named filter criteria name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets a query parameter for the named filter criteria being constructed.
     *
     * @param string|integer $key   The parameter position or name.
     * @param mixed          $value The parameter value.
     * @param string|null    $type  PDO::PARAM_* or \Doctrine\DBAL\Types\Type::* constant
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria
     */
    public function setParameter($key, $value, $type = null)
    {
        $this->query->setParameter($key, $value, $type);

        return $this;
    }

    /**
     * Sets a collection of query parameters for the named filter criteria being constructed.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection|array $parameters The query parameters to set.
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria
     */
    public function setParameters($parameters)
    {
        $this->query->setParameters($parameters);

        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The currently defined query parameters.
     */
    public function getParameters()
    {
        return $this->query->getParameters();
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed.
     *
     * @param mixed $key The key (index or name) of the bound parameter.
     *
     * @return mixed The value of the bound parameter.
     */
    public function getParameter($key)
    {
        return $this->query->getParameter($key);
    }

    /**
     * Sets the position of the first result to retrieve (the "offset").
     *
     * @param integer $firstResult The first result to return.
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria
     */
    public function setFirstResult($firstResult)
    {
        $this->query->setFirstResult($firstResult);

        return $this;
    }

    /**
     * Gets the position of the first result the query object was set to retrieve (the "offset").
     * Returns NULL if {@link setFirstResult} was not applied to this NamedCriteria.
     *
     * @return integer The position of the first result.
     */
    public function getFirstResult()
    {
        return $this->query->getFirstResult();
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit").
     *
     * @param integer $maxResults The maximum number of results to retrieve.
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria
     */
    public function setMaxResults($maxResults)
    {
        $this->query->setMaxResults($maxResults);

        return $this;
    }

    /**
     * Gets the maximum number of results the query object was set to retrieve (the "limit").
     * Returns NULL if {@link setMaxResults} was not applied to this named filter criteria.
     *
     * @return integer Maximum number of results.
     */
    public function getMaxResults()
    {
        return $this->query->getMaxResults();
    }

    /**
     * Gets a string representation of this NamedCriteria which corresponds to
     * the final DQL query being constructed.
     *
     * @return string The string representation of this NamedCriteria.
     */
    public function __toString()
    {
        return $this->query->getDQL();
    }
}
