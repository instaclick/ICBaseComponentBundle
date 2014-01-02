<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Entity\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use IC\Bundle\Base\ComponentBundle\Exception\ServiceException;
use IC\Bundle\Base\ComponentBundle\Entity\Entity;
use IC\Bundle\Base\ComponentBundle\Entity\Filter;
use Symfony\Bridge\Monolog\Logger;

/**
 * Custom implementation of Repository.
 *
 * Responsible for the contract that needs to be filled by every Repository on
 * the platform, defines the methods that need to be available.
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 * @author Anthon Pang <anthonp@nationalfibre.net>
 */
class EntityRepository implements ObjectRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var \Symfony\Bridge\Monolog\Logger Logger
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\EntityManager         $entityManager The EntityManager to use.
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata The class descriptor.
     */
    public function __construct(EntityManager $entityManager, ClassMetadata $classMetadata)
    {
        $this->entityManager = $entityManager;
        $this->classMetadata = $classMetadata;
    }

    /**
     * Define the associated optional Logger instance.
     *
     * @param \Symfony\Bridge\Monolog\Logger $logger Logger
     */
    public function setLogger(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Retrieve the associated ClassMetadata.
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->classMetadata->name;
    }

    /**
     * Retrieve the package name.
     *
     * @return string
     */
    public function getPackageName()
    {
        $namespacePartList = explode('\\', $this->classMetadata->name);

        return \Doctrine\Common\Util\Inflector::tableize($namespacePartList[2]);
    }

    /**
     * Retrieve the sub-package name.
     *
     * @return string
     */
    public function getSubPackageName()
    {
        $namespacePartList = explode('\\', $this->classMetadata->name);

        return \Doctrine\Common\Util\Inflector::tableize(str_replace('Bundle', '', $namespacePartList[3]));
    }

    /**
     * Retrieve the entity name.
     *
     * @return string
     */
    public function getEntityName()
    {
        $namespacePartList = explode('\\', $this->classMetadata->name);

        return \Doctrine\Common\Util\Inflector::tableize($namespacePartList[5]);
    }

    /**
     * {@inheritdoc}
     *
     * {@internal Contract implementation of ObjectRepository. Should not be used. }}
     */
    public function find($id)
    {
        return $this->get($id);
    }

    /**
     * {@inheritdoc}
     *
     * {@internal Contract implementation of ObjectRepository. Should not be used. }}
     */
    public function findAll()
    {
        return $this->findBy(array());
    }

    /**
     * {@inheritdoc}
     *
     * {@internal Contract implementation of ObjectRepository. Should not be used. }}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $persister  = $unitOfWork->getEntityPersister($this->classMetadata->name);

        return $persister->loadAll($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     *
     * {@internal Contract implementation of ObjectRepository. Should not be used. }}
     */
    public function findOneBy(array $criteria)
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $persister  = $unitOfWork->getEntityPersister($this->classMetadata->name);

        return $persister->load($criteria, null, null, array(), 0, 1);
    }

    /**
     * Schedule a given Entity to be persisted.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Entity $entity
     *
     * @todo Consider a possible rename to scheduleToPersist?
     */
    public function persist(Entity $entity)
    {
        $this->entityManager->persist($entity);
    }

    /**
     * Schedule a given Entity to be removed.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Entity $entity
     *
     * @todo Consider a possible rename to scheduleToRemove?
     */
    public function remove(Entity $entity)
    {
        $this->entityManager->remove($entity);
    }

    /**
     * Retrieve a new instance of Entity.
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Entity
     */
    public function newInstance()
    {
        return $this->classMetadata->getReflectionClass()->newInstance();
    }

    /**
     * Create a filter criteria builder.
     *
     * @param string $alias
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria
     */
    public function newCriteria($alias = 'e')
    {
        return $this->newBlankCriteria($alias);
    }

    /**
     * Create a named filter criteria.
     *
     * @param string $name
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\NamedCriteria
     */
    public function newNamedCriteria($name)
    {
        return new Filter\NamedCriteria($this->entityManager, $this->classMetadata->name, $name);
    }

    /**
     * Create a blank filter criteria builder.
     *
     * @param string $alias
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Filter\Criteria
     */
    public function newBlankCriteria($alias = 'e')
    {
        return new Filter\Criteria($this->entityManager, $this->classMetadata->name, $alias);
    }

    /**
     * Return a filtered list of resources.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Filter\CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function filter(Filter\CriteriaInterface $criteria)
    {
        $query = $criteria->getQuery();

        try {
            $result = new ArrayCollection($query->execute());
        } catch (\Exception $exception) {
            $this->handleException(
                $exception,
                sprintf('Unable to filter a list of %s. %s', $this->getShortClassName(), $exception->getMessage())
            );
        }

        // Extract single result if limit === 1
        if ($query->getMaxResults() === 1) {
            return ($result->count() > 0) ? $result->first() : null;
        }

        return $result;
    }

    /**
     * Retrieve a resource by its identifier.
     *
     * @param mixed $id Object identifier
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Entity
     */
    public function get($id)
    {
        try {
            $result = $this->entityManager->find($this->classMetadata->name, $id);
        } catch (\Exception $exception) {
            $this->handleException(
                $exception,
                sprintf(
                    'Unable to retrieve %s with ID: "%s"',
                    $this->getShortClassName(),
                    $id
                )
            );
        }

        return $result;
    }

    /**
     * Delete an existing resource by its identifier
     *
     * @param integer|string $id Object identifier
     */
    public function delete($id)
    {
        $this->deleteList(new ArrayCollection(array($id)));
    }

    /**
     * Delete existing resources by its identifier
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $idList
     */
    public function deleteList(Collection $idList)
    {
        $connection  = $this->entityManager->getConnection();

        try {
            $connection->beginTransaction();

            foreach ($idList as $id) {
                $proxyEntity = $this->entityManager->getReference($this->classMetadata->name, $id);

                $this->remove($proxyEntity);
            }

            $this->entityManager->flush();

            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollback();

            $this->handleException(
                $exception,
                sprintf(
                    'Unable to delete %s with IDs: "%s"',
                    $this->getShortClassName(),
                    implode('", "', $idList->toArray())
                )
            );
        }
    }

    /**
     * Creates a new resource
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Entity $entity Entity
     *
     * @throws \IC\Bundle\Base\ComponentBundle\Service\ServiceException
     */
    public function post(Entity $entity)
    {
        if ($entity->getId() !== null) {
            throw new ServiceException(sprintf('%s ID should not be set.', $this->getShortClassName()), 400);
        }

        $this->save($entity);
    }

    /**
     * Update an existing resource
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Entity $entity Entity
     *
     * @throws \IC\Bundle\Base\ComponentBundle\Service\ServiceException
     */
    public function put(Entity $entity)
    {
        if ($entity->getId() === null) {
            throw new ServiceException(sprintf('%s ID must be set.', $this->getShortClassName()), 400);
        }

        $this->save($entity);
    }

    /**
     * Save entity. Common behaviour in post and put.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Entity $entity
     */
    protected function save(Entity $entity)
    {
        $connection = $this->entityManager->getConnection();

        try {
            $connection->beginTransaction();

            $this->persist($entity);

            $this->entityManager->flush();

            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollback();

            $message = ($entity->getId() === null)
                ? sprintf('Unable to save new %s.', $this->getShortClassName())
                : sprintf('Unable to save %s with ID: %s', $this->getShortClassName(), $entity->getId());

            $this->handleException($exception, $message);
        }
    }

    /**
     * Handle internal exception.
     *
     * @param \Exception $exception Exception
     * @param string     $message   Message
     *
     * @throws \IC\Bundle\Base\ComponentBundle\Exception\ServiceException
     */
    protected function handleException(\Exception $exception, $message)
    {
        $this->logException($exception);

        // Hide internal failures
        if ($exception instanceof \PDOException) {
            $exception = null;
        }

        throw new ServiceException($message, 500, $exception);
    }

    /**
     * Log an Exception.
     *
     * @param \Exception $exception The exception thrown
     */
    private function logException(\Exception $exception)
    {
        if ($this->logger) {
            $this->logger->err(sprintf('[%s] %s', $this->getShortClassName(), $exception->getMessage()));
        }
    }

    /**
     * Retrieve a short entity class name string.
     *
     * @return string
     */
    private function getShortClassName()
    {
        return $this->classMetadata->getReflectionClass()->getShortName();
    }
}
