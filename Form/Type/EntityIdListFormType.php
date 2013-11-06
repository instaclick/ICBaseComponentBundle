<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Type;

use IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityListToEntityIdListTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Entity Id List Form Type
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class EntityIdListFormType extends AbstractType
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

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
        $builder->addModelTransformer(
            $this->getDataTransformer($options['service'], $options['named_criteria'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'named_criteria' => null
        ));

        $resolver->setRequired(array(
            'data_class',
            'service'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ic_base_component_entity_id_list';
    }

    /**
     * Creates a instance of the entity list data transformer
     *
     * @param object $service       Service
     * @param string $namedCriteria Named criteria
     *
     * @return \IC\Bundle\Base\ComponentBundle\Form\DataTransformer\EntityListToEntityIdListTransformer
     */
    private function getDataTransformer($service, $namedCriteria)
    {
        return new EntityListToEntityIdListTransformer($this->getService($service), $namedCriteria);
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
