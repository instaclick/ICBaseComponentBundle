<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\NamingStrategy;

use Doctrine\ORM\Mapping\NamingStrategy;

/**
 * Instaclick Naming Strategy
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class InstaclickNamingStrategy implements NamingStrategy
{
    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        // Skip third party bundles, can't trust their conventions
        if (stripos($className, 'IC\\') === false) {
            return $className;
        }

        list(,, $package, $subpackage,, $entity) = explode('\\', $className);

        $subpackage = str_replace('Bundle', '', $subpackage);

        if ($subpackage == $entity) {
            $entity = 'Entity';
        }

        return $package . $subpackage . $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $propertyName;
    }

    /**
     * {@inheritdoc}
     */
    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
    {
        return $propertyName . '_' . $embeddedColumnName;
    }

    /**
     * {@inheritdoc}
     */
    public function referenceColumnName()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function joinColumnName($propertyName)
    {
        return $propertyName . '_' . $this->referenceColumnName();
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return $this->classToTableName($sourceEntity) . '_' . $this->classToTableName($targetEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return strtolower(
            $this->classToTableName($entityName) . '_' .
            ($referencedColumnName ?: $this->referenceColumnName())
        );
    }
}
