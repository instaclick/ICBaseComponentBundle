<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Extension\Core\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use IC\Bundle\Base\ComponentBundle\Entity\Entity;

/**
 * A ChoiceList that ONLY works with IC\Bundle\Base\ComponentBundle\Entity\Entity
 *
 * @author Clive Zagno <clivez@nationalfibre.net>
 */
class EntityChoiceList extends ObjectChoiceList
{
    /*
     * {@inheritdoc}
     */
    protected function isPreferred($choice, array $preferredChoices)
    {
        if ( ! $choice instanceof Entity) {
            throw new \InvalidArgumentException('Choice is not of type IC\Bundle\Base\ComponentBundle\Entity\Entity');
        }

        return false !== array_search($choice->getId(), $preferredChoices, true);
    }
}
