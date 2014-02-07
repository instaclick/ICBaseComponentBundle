<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Subscriber;

use Doctrine\ORM\QueryBuilder;
use IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Dependent Entity Form Subscriber
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class DependentEntityFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\Service\Filter\EntityFormFilterServiceInterface
     */
    private $filterService;

    /**
     * @var \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    private $builder;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface                                                  $builder
     * @param \IC\Bundle\Base\ComponentBundle\Form\Service\Filter\DependentEntityFormFilterServiceInterface $filterService
     */
    public function __construct(FormBuilderInterface $builder, DependentEntityFormFilterServiceInterface $filterService)
    {
        $this->filterService = $filterService;
        $this->builder       = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => array('onFieldRebind'),
            FormEvents::PRE_SUBMIT   => array('onFieldRebind'),
        );
    }

    /**
     * Rebind the field to an entity type with the dependent criteria build.
     *
     * @param Symfony\Component\Form\FormEvent $event
     */
    public function onFieldRebind(FormEvent $event)
    {
        if ( ! $data = $event->getData()) {
            return;
        }

        $form           = $event->getForm();
        $optionList     = $this->builder->getOptions();
        $dependentValue = $this->getDependentValue($data, $optionList['dependentField']);

        // If our dependent value was null, we revert back to the choice list defined by the original type
        if ( ! $dependentValue) {
            return;
        }

        $criteria       = $this->filterService->buildCriteria($optionList['em'], $dependentValue, $optionList);
        $optionList     = $this->buildOptionList($criteria, $optionList);

        $form->add($this->builder->getName(), 'entity', $optionList);
    }

    /**
     * Build the entity option list from the original type options.
     *
     * @param \Doctrine\ORM\QueryBuilder $criteria
     * @param array                      $optionList
     *
     * @return array
     */
    private function buildOptionList(QueryBuilder $criteria, array $optionList)
    {
        return array(
            'class'         => $optionList['class'],
            'property'      => $optionList['property'],
            'query_builder' => $criteria,
            'empty_value'   => $optionList['empty_value'],
            'multiple'      => $optionList['multiple'],
            'expanded'      => $optionList['expanded'],
        );
    }

    /**
     * Retrieve the dependent field value from the parent data set.
     *
     * @param mixed  $data
     * @param string $field
     *
     * @return mixed
     */
    private function getDependentValue($data, $field)
    {
        $accessor = PropertyAccess::getPropertyAccessor();

        return $accessor->getValue((object) $data, $field);
    }
}
