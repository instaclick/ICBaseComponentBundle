<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Doctrine;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class customize entity listener resolver
 *
 * @author David Maignan <davidmaignan@gmail.com>
 */
class EntityListenerResolver extends DefaultEntityListenerResolver
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $mapping;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mapping = array();
    }

    /**
     * Map service id
     *
     * @param string $className
     * @param string $service
     */
    public function addMapping($className, $service)
    {
        $this->mapping[$className] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($className)
    {
        if (isset($this->mapping[$className]) && $this->container->has($this->mapping[$className])) {
            return $this->container->get($this->mapping[$className]);
        }

        return parent::resolve($className);
    }
}
