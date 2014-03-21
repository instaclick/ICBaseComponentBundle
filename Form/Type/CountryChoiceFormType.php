<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;

/**
 * Country Choice Form Type
 *
 * @author Clive Zagno <clivez@nationalfibre.net>
 */
class CountryChoiceFormType extends AbstractType
{
    /**
     * @var \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository
     *
     */
    private $countryRepository;

    /**
     * A List of preffered coutries
     *
     * @var array
     */
    private $preferredCountryList = array('CA', 'US');

    /**
     * Set Country Repository
     *
     * @param \IC\Bundle\Base\ComponentBundle\Entity\Repository\EntityRepository $countryRepository
     */
    public function setCountryRepository(EntityRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choice_list' => new ObjectChoiceList(
                $this->countryRepository->getCountryMap(),
                'name',
                $this->countryRepository->getPreferredCountryMap($this->preferredCountryList),
                null,
                'id'
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ic_base_component_country_choice';
    }
}
