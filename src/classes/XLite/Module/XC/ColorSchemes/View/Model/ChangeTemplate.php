<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes\View\Model;

/**
 * Change template
 */
class ChangeTemplate extends \XLite\View\Model\ChangeTemplate implements \XLite\Base\IDecorator
{
    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $result = parent::getModelObjectValue($name);

        switch ($name) {
            case 'template':
                $module = \XLite\Core\Database::getRepo('XLite\Model\Module')
                    ->findOneByModuleName('XC\ColorSchemes');

                if ($result === $module->getModuleId()) {
                    $result = \XLite\Core\Layout::getInstance()->getLayoutColor()
                        ?: \XLite\View\FormField\Select\Template::SKIN_STANDARD;
                }
                break;

            default:
        }

        return $result;
    }
}
