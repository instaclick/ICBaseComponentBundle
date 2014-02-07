<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Type;

use IC\Bundle\Base\ComponentBundle\Form\Subscriber\DependentEntityFormSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Dependent Entity Form Type
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class DependentEntityFilterFormType extends AbstractType
{
    /**
     * Define the container.
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
        if ( ! isset($options['eventDispatcher']) || ! $options['eventDispatcher'] instanceof EventDispatcherInterface) {
            return;
        }

        $service    = $this->container->get($options['service']);
        $subscriber = new DependentEntityFormSubscriber($builder, $service);
        $dispatcher = $options['eventDispatcher'];

        $dispatcher->addSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'dependentField',
            'eventDispatcher',
            'service',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ic_base_component_dependent_entity_filter';
    }
}
