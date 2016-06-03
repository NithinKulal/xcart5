<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * ThemeTweaker controller
 */
class LayoutEdit extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'apply_changes';
        $list[] = 'disable';

        return $list;
    }

    protected function doActionApplyChanges()
    {
        $changes = \XLite\Core\Request::getInstance()->changes;
        if ($changes) {
            \XLite\Core\Database::getRepo('XLite\Model\ViewList')->updateOverrides($changes);
        }

        $this->set('silent', true);
        $this->setSuppressOutput(true);
    }

    /**
     * Disable editor
     *
     * @return void
     */
    protected function doActionDisable()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\ThemeTweaker',
                'name'     => 'layout_mode',
                'value'    => false,
            )
        );

        \XLite\Core\TopMessage::addInfo('Layout editor is disabled');

        $this->setReturnURL($this->getReturnURL());

        $this->setHardRedirect(true);
    }
}
