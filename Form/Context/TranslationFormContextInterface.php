<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Context;

/**
 * Form Context Aware Interface
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
interface TranslationFormContextInterface extends FormContextInterface
{
    /**
     * Retrieve the associated translation gender.
     *
     * @return string
     */
    public function getTranslationGender();

    /**
     * Retrieve the associated translation pluralization.
     *
     * @return integer
     */
    public function getTranslationPluralization();
}
