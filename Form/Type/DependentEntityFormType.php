<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Type;

use IC\Bundle\Base\ComponentBundle\Form\Subscriber\DependentEntityFormSubscriber;
use IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Dependent Entity Form Type
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class DependentEntityFormType extends AbstractType
{
    /**
     * Define the Entity Form Filter Service.
     *
     * @param \IC\Bundle\Base\ComponentBundle\Form\Service\Filter\EntityFormFilterService $filterService
     */
    public function setEntityFormFilterService(DependentEntityFormFilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ( ! isset($options['eventDispatcher']) || ! $options['eventDispatcher'] instanceof EventDispatcherInterface) {
            return;
        }

        $service    = $this->filterService;
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
            'dependentProperty',
            'eventDispatcher',
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
        return 'ic_base_component_dependent_entity';
    }
}
