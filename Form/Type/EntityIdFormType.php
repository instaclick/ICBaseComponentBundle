<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Type;

use IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository;
use IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityToEntityIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Entity Id Form Type
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 * @author John Zhang <johnz@nationalfibre.net>
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class EntityIdFormType extends AbstractType
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $parent;

    /**
     * @var string
     */
    private $name;

    /**
     * Define Container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->getDataTransformer($options));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'data_class',
            'service'
        ));

        $resolver->setOptional(array(
            'default_id',
            'property'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Creates a instance of the entity list data transformer
     *
     * @param array $options
     *
     * @return \IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityListToEntityIdListTransformer
     */
    private function getDataTransformer($options)
    {
        $entityRepository = $this->getService($options['service']);
        $property         = $this->getProperty($entityRepository, $options);
        $defaultId        = isset($options['default_id']) ? $options['default_id'] : null;

        return new EntityToEntityIdTransformer($entityRepository, $property, $defaultId);
    }

    /**
     * Retrieve the property used for the transformation.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository $entityRepository
     * @param array                                                              $options
     *
     * @return string
     */
    private function getProperty(EntityRepository $entityRepository, array $options)
    {
        $classMetadata = $entityRepository->getClassMetadata();

        if ( ! isset($options['property'])) {
            return $classMetadata->getSingleIdentifierColumnName();
        }

        if ( ! $classMetadata->hasField($options['property'])) {
            throw new \InvalidArgumentException(
                sprintf('Entity [%s] does not contain field [%s]', $entityRepository->getEntityName(), $options['property'])
            );
        }

        if ( ! $classMetadata->isUniqueField($options['property'])) {
            throw new \InvalidArgumentException(
                sprintf('Entity [%s] field [%s] must be marked as unique', $entityRepository->getEntityName(), $options['property'])
            );
        }

        return $options['property'];
    }

    /**
     * Converts a service identifier to a service object
     *
     * @param mixed $service
     *
     * @return object
     */
    private function getService($service)
    {
        return is_string($service) ? $this->container->get($service) : $service;
    }
}
