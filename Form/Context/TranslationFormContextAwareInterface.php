<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Form\Context;

/**
 * Translation Form Context Aware Interface
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 */
interface TranslationFormContextAwareInterface
{
    /**
     * Define the translation form context
     *
     * @param \IC\Bundle\Base\ComponentBundle\Form\Context\TranslationFormContextInterface $translationFormContext
     */
    public function setTranslationFormContext(TranslationFormContextInterface $translationFormContext);
}
