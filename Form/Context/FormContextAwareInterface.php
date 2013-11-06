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
interface FormContextAwareInterface
{
    /**
     * Define the form context
     *
     * @param \IC\Bundle\Base\ComponentBundle\Form\Context\FormContextInterface $formContext
     */
    public function setFormContext(FormContextInterface $formContext);
}
