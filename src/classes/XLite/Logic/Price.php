<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

/**
 * Price 
 */
class Price extends \XLite\Logic\ALogic
{
    /**
     * Modifiers 
     * 
     * @var array
     */
    protected $modifiers;

    /**
     * Apply price modifiers
     * 
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $method    Model's getter
     * @param array                $behaviors Behaviors OPTIONAL
     * @param string               $purpose   Purpose OPTIONAL
     *  
     * @return float
     */
    public function apply(\XLite\Model\AEntity $model, $method, array $behaviors = array(), $purpose = 'net')
    {
        $property = lcfirst(substr($method, 3));
        $value = $model->$method();

        $modifiers = $this->prepareModifiers($this->getModifiers(), $behaviors, $purpose);
        foreach ($modifiers as $modifier) {
            $value = $modifier->apply($value, $model, $property, $behaviors, $purpose);
        }

        return $value;
    }

    /**
     * Get modifiers 
     * 
     * @return array
     */
    protected function getModifiers()
    {
        if (!isset($this->modifiers)) {
            $this->modifiers = $this->defineModifiers();
        }

        return $this->modifiers;
    }

    /**
     * Define modifiers 
     * 
     * @return array
     */
    protected function defineModifiers()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\MoneyModificator')->findActive();
    }

    /**
     * Prepare modifiers 
     * 
     * @param array  $modifiers Modifiers list
     * @param array  $behaviors Behaviors
     * @param string $purpose   Purpose
     *  
     * @return array
     */
    protected function prepareModifiers(array $modifiers, array $behaviors, $purpose)
    {
        foreach($modifiers as $i => $modifier) {
            if (
                ($modifier->getPurpose() && $modifier->getPurpose() != $purpose)
                || ($modifier->getBehavior() && !in_array($modifier->getBehavior(), $behaviors))
            ) {
                unset($modifiers[$i]);
            }
        }

        return $modifiers;
    }
}

